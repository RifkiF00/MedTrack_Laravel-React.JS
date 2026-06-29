<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class AsetController extends Controller
{
    private function checkIpsrs()
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['Admin_IPSRS', 'Staf_IPSRS'])) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Staf IPSRS.');
        }
    }
    /**
     * Tampilkan Daftar Aset (dengan Filter Pencarian & Kategori)
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $kategori = $request->input('kategori');
        $kondisi = $request->input('kondisi');

        $query = Aset::with(['ruangan']);

        $user = Auth::user();
        if ($user && $user->role === 'Unit_RS' && $user->id_ruang) {
            $query->where('id_ruang_saat_ini', $user->id_ruang);
        }

        if ($search) {
            // Jika pencarian cocok persis dengan kode_label (hasil scan QR Code)
            $exactAset = Aset::where('kode_label', $search)->first();
            if ($exactAset) {
                return redirect()->route('aset.show', $exactAset->id_aset);
            }

            $query->where(function ($q) use ($search) {
                $q->where('nama_alat', 'like', "%{$search}%")
                  ->orWhere('kode_label', 'like', "%{$search}%")
                  ->orWhere('merk', 'like', "%{$search}%");
            });
        }

        if ($kategori) {
            $query->where('kategori_aset', $kategori);
        }

        if ($kondisi) {
            $query->where('status_kondisi', $kondisi);
        }

        $asets = $query->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

        return Inertia::render('Aset/Index', [
            'asets' => $asets,
            'filters' => [
                'search' => $search,
                'kategori' => $kategori,
                'kondisi' => $kondisi,
            ]
        ]);
    }

    /**
     * Halaman Input Aset Baru
     */
    public function create()
    {
        $this->checkIpsrs();
        $ruangans = Ruangan::all();

        return Inertia::render('Aset/Create', [
            'ruangans' => $ruangans
        ]);
    }

    /**
     * Simpan Aset Baru & Auto-generate QR Code
     */
    public function store(Request $request)
    {
        $this->checkIpsrs();
        $request->validate([
            'kode_label' => 'required|string|unique:m_aset,kode_label',
            'nama_alat' => 'required|string|max:150',
            'kategori_aset' => 'required|in:Medis,Sarpras,IT',
            'status_kondisi' => 'required|in:Baik,Rusak_Ringan,Rusak_Berat,Maintenance,Gudang,Pensiun',
            'id_ruang_saat_ini' => 'nullable|integer|exists:m_ruangan,id_ruang',
            'jumlah_unit' => 'required|integer|min:1',
            'merk' => 'nullable|string|max:80',
            'model' => 'nullable|string|max:80',
            'serial_number' => 'nullable|string|max:100',
            'no_sertifikat' => 'nullable|string|max:50',
            'tgl_pengadaan' => 'nullable|date',
            'tgl_kalibrasi_terakhir' => 'nullable|date',
            'harga_perolehan' => 'nullable|numeric',
            'lokasi_fisik' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'gambar_aset_file' => 'nullable|image|max:5120' // Max 5MB
        ]);

        $data = $request->except(['gambar_aset_file']);

        // Auto-synchronize expiration date with calibration date if empty
        if (!empty($data['tgl_kalibrasi_terakhir']) && empty($data['tgl_kadaluarsa_sertif'])) {
            $data['tgl_kadaluarsa_sertif'] = $data['tgl_kalibrasi_terakhir'];
        }

        // Handle Image Upload jika ada
        if ($request->hasFile('gambar_aset_file')) {
            $file = $request->file('gambar_aset_file');
            $fileName = 'aset_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/assets'), $fileName);
            $data['gambar_aset'] = $fileName;
        }

        // Simpan data aset
        $aset = Aset::create($data);

        // Generate QR Code untuk Aset
        try {
            $qrDir = public_path('uploads/qr');
            if (!File::isDirectory($qrDir)) {
                File::makeDirectory($qrDir, 0777, true, true);
            }

            $fileName = 'aset_' . $aset->id_aset . '.png';
            $filePath = $qrDir . '/' . $fileName;

            // Menggunakan API QR Server untuk mendownload QR image secara lokal
            $qrData = route('aset.show', $aset->id_aset);
            $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);
            
            $qrImageContent = file_get_contents($qrUrl);
            if ($qrImageContent !== false) {
                File::put($filePath, $qrImageContent);
                $aset->update(['file_qr_code' => $fileName]);
            }
        } catch (\Exception $e) {
            // Log error tapi jangan gagalkan submit form
            logger()->error('Gagal generate QR Code: ' . $e->getMessage());
        }

        return Redirect::route('aset.index')->with('success', 'Aset medis berhasil ditambahkan.');
    }

    /**
     * Detail Aset (Scan View)
     */
    public function show($id)
    {
        $aset = Aset::with(['ruangan', 'trackings.user', 'maintenances.teknisi'])->findOrFail($id);

        return Inertia::render('Aset/Detail', [
            'aset' => [
                'id_aset' => $aset->id_aset,
                'kode_label' => $aset->kode_label,
                'nama_alat' => $aset->nama_alat,
                'kategori_aset' => $aset->kategori_aset,
                'merk' => $aset->merk,
                'model' => $aset->model,
                'serial_number' => $aset->serial_number,
                'no_sertifikat' => $aset->no_sertifikat,
                'tgl_pengadaan' => $aset->tgl_pengadaan ? $aset->tgl_pengadaan->format('Y-m-d') : null,
                'tgl_kalibrasi_terakhir' => $aset->tgl_kalibrasi_terakhir ? $aset->tgl_kalibrasi_terakhir->format('Y-m-d') : null,
                'status_kondisi' => $aset->status_kondisi,
                'ruangan' => $aset->ruangan ? $aset->ruangan->nama_ruang : 'Tidak ada',
                'lokasi_fisik' => $aset->lokasi_fisik,
                'keterangan' => $aset->keterangan,
                'gambar_aset' => $aset->gambar_aset ? asset('uploads/assets/' . $aset->gambar_aset) : null,
                'file_qr_code' => $aset->file_qr_code ? asset('uploads/qr/' . $aset->file_qr_code) : null,
                'latitude' => $aset->latitude,
                'longitude' => $aset->longitude,
            ],
            'trackings' => $aset->trackings->map(function ($t) {
                return [
                    'id_track' => $t->id_track,
                    'user' => $t->user ? $t->user->nama_lengkap : 'N/A',
                    'ruangan' => $t->ruangan ? $t->ruangan->nama_ruang : 'N/A',
                    'latitude' => $t->latitude,
                    'longitude' => $t->longitude,
                    'tgl_update' => $t->tgl_update->format('Y-m-d H:i'),
                    'keterangan' => $t->keterangan
                ];
            }),
            'maintenances' => $aset->maintenances->map(function ($m) {
                return [
                    'id_main' => $m->id_main,
                    'teknisi' => $m->teknisi ? $m->teknisi->nama_lengkap : 'N/A',
                    'jenis_tindakan' => $m->jenis_tindakan,
                    'tgl_mulai' => $m->tgl_mulai->format('Y-m-d H:i'),
                    'status_perbaikan' => $m->status_perbaikan,
                    'deskripsi_kendala' => $m->deskripsi_kendala,
                ];
            })
        ]);
    }

    /**
     * Halaman Edit Aset
     */
    public function edit($id)
    {
        $this->checkIpsrs();
        $aset = Aset::findOrFail($id);
        $ruangans = Ruangan::all();

        return Inertia::render('Aset/Edit', [
            'aset' => [
                'id_aset' => $aset->id_aset,
                'kode_label' => $aset->kode_label,
                'nama_alat' => $aset->nama_alat,
                'kategori_aset' => $aset->kategori_aset,
                'merk' => $aset->merk,
                'model' => $aset->model,
                'serial_number' => $aset->serial_number,
                'no_sertifikat' => $aset->no_sertifikat,
                'tgl_pengadaan' => $aset->tgl_pengadaan ? $aset->tgl_pengadaan->format('Y-m-d') : '',
                'tgl_kalibrasi_terakhir' => $aset->tgl_kalibrasi_terakhir ? $aset->tgl_kalibrasi_terakhir->format('Y-m-d') : '',
                'tgl_kadaluarsa_sertif' => $aset->tgl_kadaluarsa_sertif ? $aset->tgl_kadaluarsa_sertif->format('Y-m-d') : '',
                'harga_perolehan' => $aset->harga_perolehan,
                'status_kondisi' => $aset->status_kondisi,
                'id_ruang_saat_ini' => $aset->id_ruang_saat_ini,
                'lokasi_fisik' => $aset->lokasi_fisik,
                'keterangan' => $aset->keterangan,
                'gambar_aset' => $aset->gambar_aset ? asset('uploads/assets/' . $aset->gambar_aset) : null,
                'latitude' => $aset->latitude,
                'longitude' => $aset->longitude,
                'jumlah_unit' => $aset->jumlah_unit,
            ],
            'ruangans' => $ruangans
        ]);
    }

    /**
     * Perbarui Data Aset
     */
    public function update(Request $request, $id)
    {
        $this->checkIpsrs();
        $aset = Aset::findOrFail($id);

        $request->validate([
            'kode_label' => 'required|string|unique:m_aset,kode_label,' . $id . ',id_aset',
            'nama_alat' => 'required|string|max:150',
            'kategori_aset' => 'required|in:Medis,Sarpras,IT',
            'status_kondisi' => 'required|in:Baik,Rusak_Ringan,Rusak_Berat,Maintenance,Gudang,Pensiun',
            'id_ruang_saat_ini' => 'nullable|integer|exists:m_ruangan,id_ruang',
            'jumlah_unit' => 'required|integer|min:1',
            'merk' => 'nullable|string|max:80',
            'model' => 'nullable|string|max:80',
            'serial_number' => 'nullable|string|max:100',
            'no_sertifikat' => 'nullable|string|max:50',
            'tgl_pengadaan' => 'nullable|date',
            'tgl_kalibrasi_terakhir' => 'nullable|date',
            'tgl_kadaluarsa_sertif' => 'nullable|date',
            'harga_perolehan' => 'nullable|numeric',
            'lokasi_fisik' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'gambar_aset_file' => 'nullable|image|max:5120'
        ]);

        $data = $request->except(['gambar_aset_file']);

        // Auto-synchronize expiration date with calibration date if empty
        if (!empty($data['tgl_kalibrasi_terakhir']) && empty($data['tgl_kadaluarsa_sertif'])) {
            $data['tgl_kadaluarsa_sertif'] = $data['tgl_kalibrasi_terakhir'];
        }

        // Handle Image Upload
        if ($request->hasFile('gambar_aset_file')) {
            $file = $request->file('gambar_aset_file');
            $fileName = 'aset_' . time() . '_' . rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/assets'), $fileName);
            $data['gambar_aset'] = $fileName;

            // Delete old image
            if ($aset->gambar_aset && File::exists(public_path('uploads/assets/' . $aset->gambar_aset))) {
                File::delete(public_path('uploads/assets/' . $aset->gambar_aset));
            }
        }

        // Update database
        $aset->update($data);

        // Regenerate/Update QR Code if kode_label changed
        if ($request->kode_label !== $aset->getOriginal('kode_label') || !$aset->file_qr_code) {
            try {
                $qrDir = public_path('uploads/qr');
                $fileName = 'aset_' . $aset->id_aset . '.png';
                $filePath = $qrDir . '/' . $fileName;

                // Delete old QR file if exists
                if ($aset->file_qr_code && File::exists($qrDir . '/' . $aset->file_qr_code)) {
                    File::delete($qrDir . '/' . $aset->file_qr_code);
                }

                $qrData = route('aset.show', $aset->id_aset);
                $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);
                
                $qrImageContent = file_get_contents($qrUrl);
                if ($qrImageContent !== false) {
                    File::put($filePath, $qrImageContent);
                    $aset->update(['file_qr_code' => $fileName]);
                }
            } catch (\Exception $e) {
                logger()->error('Gagal regenerasi QR Code: ' . $e->getMessage());
            }
        }

        return Redirect::route('aset.index')->with('success', 'Data aset berhasil diperbarui.');
    }

    /**
     * Hapus Aset
     */
    public function destroy($id)
    {
        $this->checkIpsrs();
        $aset = Aset::findOrFail($id);

        // Delete files
        if ($aset->gambar_aset && File::exists(public_path('uploads/assets/' . $aset->gambar_aset))) {
            File::delete(public_path('uploads/assets/' . $aset->gambar_aset));
        }
        if ($aset->file_qr_code && File::exists(public_path('uploads/qr/' . $aset->file_qr_code))) {
            File::delete(public_path('uploads/qr/' . $aset->file_qr_code));
        }

        $aset->delete();

        return Redirect::route('aset.index')->with('success', 'Aset medis berhasil dihapus.');
    }
}
