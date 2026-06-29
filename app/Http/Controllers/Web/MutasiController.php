<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\Mutasi;
use App\Models\Ruangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MutasiController extends Controller
{
    private function checkAccess()
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['Admin_IPSRS', 'Staf_IPSRS', 'Staf_Logistik', 'Unit_RS'])) {
            abort(403, 'Akses ditolak.');
        }
    }

    private function checkIpsrsAccess()
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['Admin_IPSRS', 'Staf_IPSRS'])) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Staf IPSRS.');
        }
    }

    /**
     * Tampilkan Daftar Mutasi Aset
     */
    public function index()
    {
        $this->checkAccess();

        $user = Auth::user();
        $role = $user->role;
        $userId = $user->id_user;

        $query = Mutasi::with(['aset', 'ruangAsal', 'ruangTujuan', 'pencatat']);

        if ($role === 'Unit_RS') {
            // Unit hanya melihat mutasi yang mereka ajukan
            $query->where('id_user_pencatat', $userId);
        }

        $mutasiList = $query->orderBy('created_at', 'desc')->get()->map(function ($m) {
            return [
                'id_mutasi' => $m->id_mutasi,
                'nama_alat' => $m->aset ? $m->aset->nama_alat : 'N/A',
                'kode_label' => $m->aset ? $m->aset->kode_label : 'N/A',
                'ruang_asal' => $m->ruangAsal ? $m->ruangAsal->nama_ruang : 'N/A',
                'ruang_tujuan' => $m->ruangTujuan ? $m->ruangTujuan->nama_ruang : 'N/A',
                'pencatat' => $m->pencatat ? $m->pencatat->nama_lengkap : 'N/A',
                'tgl_mutasi' => $m->tgl_mutasi ? $m->tgl_mutasi->format('Y-m-d H:i') : null,
                'alasan_mutasi' => $m->alasan_mutasi,
                'status_mutasi' => $m->status_mutasi,
                'catatan' => $m->catatan
            ];
        });

        // Hitung statistik mutasi
        $statsQuery = Mutasi::query();
        if ($role === 'Unit_RS') {
            $statsQuery->where('id_user_pencatat', $userId);
        }

        $pending = (clone $statsQuery)->where('status_mutasi', 'Menunggu_Verifikasi')->count();
        $approved = (clone $statsQuery)->where('status_mutasi', 'Disetujui')->count();
        $completed = (clone $statsQuery)->where('status_mutasi', 'Selesai')->count();
        $rejected = (clone $statsQuery)->where('status_mutasi', 'Ditolak')->count();

        return Inertia::render('Mutasi/Index', [
            'mutasiList' => $mutasiList,
            'role' => $role,
            'stats' => [
                'pending' => $pending,
                'approved' => $approved,
                'completed' => $completed,
                'rejected' => $rejected
            ]
        ]);
    }

    /**
     * Halaman Catat Mutasi Baru
     */
    public function create()
    {
        $this->checkAccess();

        $user = Auth::user();
        $role = $user->role;
        $idRuang = $user->id_ruang;

        $asetQuery = Aset::query();
        // Allow all users to request mutation for any pen-placed asset
        $asetQuery->whereNotNull('id_ruang_saat_ini');

        $asets = $asetQuery->with('ruangan')->get()->map(function ($a) {
            return [
                'id_aset' => $a->id_aset,
                'kode_label' => $a->kode_label,
                'nama_alat' => $a->nama_alat,
                'id_ruang_saat_ini' => $a->id_ruang_saat_ini,
                'nama_ruang' => $a->ruangan ? $a->ruangan->nama_ruang : 'Tidak diketahui'
            ];
        });

        $ruangans = Ruangan::all();

        return Inertia::render('Mutasi/Create', [
            'asets' => $asets,
            'ruangans' => $ruangans
        ]);
    }

    /**
     * Simpan Permintaan Mutasi Baru
     */
    public function store(Request $request)
    {
        $this->checkAccess();

        $request->validate([
            'id_aset' => 'required|integer|exists:m_aset,id_aset',
            'ruang_asal' => 'required|integer|exists:m_ruangan,id_ruang',
            'ruang_tujuan' => 'required|integer|exists:m_ruangan,id_ruang|different:ruang_asal',
            'alasan_mutasi' => 'required|string|max:255',
            'catatan' => 'nullable|string'
        ]);

        $user = Auth::user();

        Mutasi::create([
            'id_aset' => $request->id_aset,
            'ruang_asal' => $request->ruang_asal,
            'ruang_tujuan' => $request->ruang_tujuan,
            'id_user_pencatat' => $user->id_user,
            'alasan_mutasi' => $request->alasan_mutasi,
            'status_mutasi' => 'Menunggu_Verifikasi',
            'catatan' => $request->catatan
        ]);

        return Redirect::route('mutasi.index')->with('success', 'Permintaan mutasi berhasil dicatat dan menunggu verifikasi.');
    }

    /**
     * Approve Mutasi (Oleh IPSRS)
     */
    public function approve($id)
    {
        $this->checkIpsrsAccess();

        $mutasi = Mutasi::findOrFail($id);
        $mutasi->update(['status_mutasi' => 'Disetujui']);

        return Redirect::route('mutasi.index')->with('success', 'Permintaan mutasi disetujui.');
    }

    /**
     * Reject Mutasi (Oleh IPSRS)
     */
    public function reject($id)
    {
        $this->checkIpsrsAccess();

        $mutasi = Mutasi::findOrFail($id);
        $mutasi->update(['status_mutasi' => 'Ditolak']);

        return Redirect::route('mutasi.index')->with('success', 'Permintaan mutasi ditolak.');
    }

    /**
     * Selesaikan Mutasi & Update Lokasi Aset Terkini
     */
    public function complete($id)
    {
        $this->checkIpsrsAccess();

        $mutasi = Mutasi::findOrFail($id);

        try {
            DB::beginTransaction();

            // 1. Update status mutasi menjadi Selesai
            $mutasi->update(['status_mutasi' => 'Selesai']);

            // 2. Update lokasi ruang saat ini di tabel m_aset
            Aset::where('id_aset', $mutasi->id_aset)->update([
                'id_ruang_saat_ini' => $mutasi->ruang_tujuan
            ]);

            DB::commit();

            return Redirect::route('mutasi.index')->with('success', 'Mutasi selesai. Lokasi penempatan aset berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::route('mutasi.index')->with('error', 'Terjadi kesalahan sistem saat memproses mutasi.');
        }
    }
}
