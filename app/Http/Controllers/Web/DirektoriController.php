<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Ruangan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class DirektoriController extends Controller
{
    private function checkAccess()
    {
        // CRUD only allowed for Admin_IPSRS or Staf_IPSRS
        return in_array(Auth::user()->role, ['Admin_IPSRS', 'Staf_IPSRS']);
    }

    public function index()
    {
        $user = Auth::user();
        
        $ruangans = Ruangan::with(['asets'])->withCount('aset')->orderBy('nama_ruang', 'asc')->get()->map(function ($r) {
            return [
                'id_ruang' => $r->id_ruang,
                'nama_ruang' => $r->nama_ruang,
                'kategori' => $r->kategori,
                'foto' => $r->foto,
                'latitude' => $r->latitude ? (float)$r->latitude : null,
                'longitude' => $r->longitude ? (float)$r->longitude : null,
                'lokasi_gedung' => $r->lokasi_gedung,
                'aset_count' => $r->aset_count,
                'asets_list' => $r->asets->map(function ($a) {
                    return [
                        'id_aset' => $a->id_aset,
                        'kode_label' => $a->kode_label,
                        'nama_alat' => $a->nama_alat,
                        'status_kondisi' => $a->status_kondisi,
                    ];
                })->values()->all(),
            ];
        });

        $sdmList = User::with('ruangan')->orderBy('nama_lengkap', 'asc')->get()->map(function ($u) {
            return [
                'id_user' => $u->id_user,
                'username' => $u->username,
                'email' => $u->email,
                'nama_lengkap' => $u->nama_lengkap,
                'role' => $u->role,
                'id_ruang' => $u->id_ruang,
                'nama_ruang' => $u->ruangan ? $u->ruangan->nama_ruang : 'Umum / Tidak Ada',
                'nip' => $u->nip,
                'no_hp' => $u->no_hp,
                'status' => $u->status,
                'kontak_darurat_1' => $u->kontak_darurat_1,
                'kontak_darurat_2' => $u->kontak_darurat_2,
                'kontak_darurat_3' => $u->kontak_darurat_3,
            ];
        });

        return Inertia::render('Direktori/Index', [
            'ruangans' => $ruangans,
            'sdmList' => $sdmList,
            'canCrud' => $this->checkAccess(),
            'currentRole' => $user->role,
        ]);
    }

    /**
     * CRUD: Ruangan
     */
    public function storeRuangan(Request $request)
    {
        if (!$this->checkAccess()) abort(403);

        $request->validate([
            'nama_ruang' => 'required|string|max:100|unique:m_ruangan,nama_ruang',
            'lokasi_gedung' => 'nullable|string|max:100',
            'kategori' => 'required|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $fotoName = null;
        if ($request->hasFile('foto_file')) {
            $file = $request->file('foto_file');
            $fotoName = 'ruangan_' . time() . '_' . mt_rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/ruangan'), $fotoName);
        }

        Ruangan::create([
            'nama_ruang' => $request->nama_ruang,
            'lokasi_gedung' => $request->lokasi_gedung,
            'kategori' => $request->kategori,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'foto' => $fotoName
        ]);

        return Redirect::route('direktori.index')->with('success', 'Ruangan berhasil ditambahkan.');
    }

    public function updateRuangan(Request $request, $id)
    {
        if (!$this->checkAccess()) abort(403);

        $ruangan = Ruangan::findOrFail($id);

        $request->validate([
            'nama_ruang' => 'required|string|max:100|unique:m_ruangan,nama_ruang,' . $id . ',id_ruang',
            'lokasi_gedung' => 'nullable|string|max:100',
            'kategori' => 'required|string|max:50',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'foto_file' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $fotoName = $ruangan->foto;
        if ($request->hasFile('foto_file')) {
            $file = $request->file('foto_file');
            $fotoName = 'ruangan_' . time() . '_' . mt_rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('uploads/ruangan'), $fotoName);
            
            // Delete old file
            if ($ruangan->foto && file_exists(public_path('uploads/ruangan/' . $ruangan->foto))) {
                @unlink(public_path('uploads/ruangan/' . $ruangan->foto));
            }
        }

        $ruangan->update([
            'nama_ruang' => $request->nama_ruang,
            'lokasi_gedung' => $request->lokasi_gedung,
            'kategori' => $request->kategori,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'foto' => $fotoName
        ]);

        return Redirect::route('direktori.index')->with('success', 'Ruangan berhasil diperbarui.');
    }

    public function deleteRuangan(Request $request, $id)
    {
        if (!$this->checkAccess()) abort(403);

        $ruangan = Ruangan::withCount('aset')->findOrFail($id);

        if ($ruangan->aset_count > 0) {
            return Redirect::back()->with('error', 'Tidak dapat menghapus ruangan yang masih memiliki aset terdaftar.');
        }

        if ($ruangan->foto && file_exists(public_path('uploads/ruangan/' . $ruangan->foto))) {
            @unlink(public_path('uploads/ruangan/' . $ruangan->foto));
        }

        $ruangan->delete();

        return Redirect::route('direktori.index')->with('success', 'Ruangan berhasil dihapus.');
    }

    /**
     * CRUD: SDM / Staff
     */
    public function storeSDM(Request $request)
    {
        if (!$this->checkAccess()) abort(403);

        $request->validate([
            'username' => 'required|string|min:5|max:50|unique:m_user,username',
            'email' => 'required|email|max:100|unique:m_user,email',
            'password' => 'required|string|min:6',
            'nama_lengkap' => 'required|string|max:100',
            'role' => 'required|string|in:Admin_IPSRS,Staf_IPSRS,Staf_Logistik,Unit_RS,Kepala_IPSRS',
            'id_ruang' => 'nullable|integer|exists:m_ruangan,id_ruang',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password_hash' => Hash::make($request->password),
            'nama_lengkap' => $request->nama_lengkap,
            'role' => $request->role,
            'id_ruang' => $request->id_ruang,
            'nip' => $request->nip,
            'no_hp' => $request->no_hp,
            'status' => $request->status,
        ]);

        return Redirect::route('direktori.index')->with('success', 'Akun staff berhasil ditambahkan.');
    }

    public function updateSDM(Request $request, $id)
    {
        if (!$this->checkAccess()) abort(403);

        $user = User::findOrFail($id);

        $request->validate([
            'username' => 'required|string|min:5|max:50|unique:m_user,username,' . $id . ',id_user',
            'email' => 'required|email|max:100|unique:m_user,email,' . $id . ',id_user',
            'password' => 'nullable|string|min:6',
            'nama_lengkap' => 'required|string|max:100',
            'role' => 'required|string|in:Admin_IPSRS,Staf_IPSRS,Staf_Logistik,Unit_RS,Kepala_IPSRS',
            'id_ruang' => 'nullable|integer|exists:m_ruangan,id_ruang',
            'nip' => 'nullable|string|max:50',
            'no_hp' => 'nullable|string|max:20',
            'status' => 'required|in:Aktif,Nonaktif',
        ]);

        $updateData = [
            'username' => $request->username,
            'email' => $request->email,
            'nama_lengkap' => $request->nama_lengkap,
            'role' => $request->role,
            'id_ruang' => $request->id_ruang,
            'nip' => $request->nip,
            'no_hp' => $request->no_hp,
            'status' => $request->status,
        ];

        if ($request->password) {
            $updateData['password_hash'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return Redirect::route('direktori.index')->with('success', 'Akun staff berhasil diperbarui.');
    }

    public function deleteSDM(Request $request, $id)
    {
        if (!$this->checkAccess()) abort(403);

        $user = User::findOrFail($id);

        if ($user->id_user === Auth::id()) {
            return Redirect::back()->with('error', 'Tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return Redirect::route('direktori.index')->with('success', 'Akun staff berhasil dihapus.');
    }

    /**
     * Update Kontak Darurat
     */
    public function updateKontak(Request $request, $id)
    {
        if (!$this->checkAccess()) abort(403);

        $user = User::findOrFail($id);

        $request->validate([
            'kontak_darurat_1' => 'nullable|string|max:50',
            'kontak_darurat_2' => 'nullable|string|max:50',
            'kontak_darurat_3' => 'nullable|string|max:50',
        ]);

        $user->update([
            'kontak_darurat_1' => $request->kontak_darurat_1,
            'kontak_darurat_2' => $request->kontak_darurat_2,
            'kontak_darurat_3' => $request->kontak_darurat_3,
        ]);

        return Redirect::route('direktori.index')->with('success', 'Kontak darurat berhasil diperbarui.');
    }
}
