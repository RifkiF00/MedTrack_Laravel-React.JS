<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\Troubleshoot;
use App\Models\TroubleshootLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class WorkOrderController extends Controller
{
    private function isIPSRS()
    {
        $role = Auth::user()->role;
        return in_array($role, ['Admin_IPSRS', 'Staf_IPSRS', 'Staf_Logistik']);
    }

    private function isUnit()
    {
        return Auth::user()->role === 'Unit_RS';
    }

    /**
     * Tampilkan daftar Work Order
     */
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;

        $workordersQuery = Troubleshoot::with([
            'aset.ruangan',
            'pelapor',
            'teknisi'
        ])->orderBy('tgl_lapor', 'desc')->orderBy('id_ticket', 'desc');

        if ($this->isIPSRS()) {
            $workorders = $workordersQuery->get();
        } else if ($this->isUnit()) {
            $workorders = $workordersQuery->where('id_user_pelapor', $user->id_user)->get();
        } else {
            abort(403, 'Akses ditolak.');
        }

        // Mapping agar format datanya seragam dan mudah dibaca oleh React component
        $workordersData = $workorders->map(function ($wo) {
            return [
                'id_ticket' => $wo->id_ticket,
                'tgl_lapor' => $wo->tgl_lapor ? $wo->tgl_lapor->format('Y-m-d H:i') : null,
                'kode_label' => $wo->aset ? $wo->aset->kode_label : 'N/A',
                'nama_alat' => $wo->aset ? $wo->aset->nama_alat : 'N/A',
                'nama_ruang' => ($wo->aset && $wo->aset->ruangan) ? $wo->aset->ruangan->nama_ruang : 'Tidak diketahui',
                'deskripsi_kerusakan' => $wo->deskripsi_kerusakan,
                'foto_kerusakan' => $wo->foto_kerusakan,
                'tingkat_urgensi' => $wo->tingkat_urgensi,
                'status_ticket' => $wo->status_ticket,
                'id_user_pelapor' => $wo->id_user_pelapor,
                'nama_pelapor' => $wo->nama_pelapor_bebas ?: ($wo->pelapor ? $wo->pelapor->nama_lengkap : 'N/A'),
                'id_teknisi_penanggungjawab' => $wo->id_teknisi_penanggungjawab,
                'nama_teknisi' => $wo->teknisi ? $wo->teknisi->nama_lengkap : 'Belum Ditugaskan',
            ];
        });

        // Ambil daftar teknisi jika user adalah IPSRS
        $teknisiList = [];
        if (in_array($role, ['Admin_IPSRS', 'Staf_IPSRS'])) {
            $teknisiList = User::whereIn('role', ['Admin_IPSRS', 'Staf_IPSRS'])->get()->map(function ($u) {
                return [
                    'id_user' => $u->id_user,
                    'nama_lengkap' => $u->nama_lengkap,
                ];
            });
        }

        return Inertia::render('WorkOrder/Index', [
            'workorders' => $workordersData,
            'teknisiList' => $teknisiList,
            'role' => $role,
        ]);
    }

    /**
     * Tampilkan form pembuatan Work Order
     */
    public function create()
    {
        if (!$this->isUnit()) {
            abort(403, 'Hanya Unit yang dapat membuat Work Order.');
        }

        $user = Auth::user();
        $idRuang = $user->id_ruang;

        // Ambil aset yang berada di ruangan unit pelapor saat ini
        $asets = [];
        if ($idRuang) {
            $asets = Aset::where('id_ruang_saat_ini', $idRuang)->get()->map(function ($a) {
                return [
                    'id_aset' => $a->id_aset,
                    'kode_label' => $a->kode_label,
                    'nama_alat' => $a->nama_alat,
                ];
            });
        }

        return Inertia::render('WorkOrder/Create', [
            'asets' => $asets,
            'defaultPelapor' => $user->nama_lengkap,
        ]);
    }

    /**
     * Simpan Work Order Baru
     */
    public function store(Request $request)
    {
        if (!$this->isUnit()) {
            abort(403, 'Akses ditolak.');
        }

        $request->validate([
            'id_aset' => 'required|integer|exists:m_aset,id_aset',
            'tingkat_urgensi' => 'required|in:Rendah,Sedang,Tinggi,Darurat',
            'deskripsi_kerusakan' => 'required|string',
            'nama_pelapor_bebas' => 'required|string|max:100',
            'foto_kerusakan' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
        ]);

        $fotoName = null;
        if ($request->hasFile('foto_kerusakan')) {
            $file = $request->file('foto_kerusakan');
            $fotoName = 'trouble_' . time() . '_' . mt_rand(1000, 9999) . '.' . $file->getClientOriginalExtension();
            
            // Simpan file ke folder public/uploads/troubleshoot
            $file->move(public_path('uploads/troubleshoot'), $fotoName);
        }

        try {
            DB::beginTransaction();

            $wo = Troubleshoot::create([
                'id_aset' => $request->id_aset,
                'id_user_pelapor' => Auth::id(),
                'nama_pelapor_bebas' => $request->nama_pelapor_bebas,
                'tingkat_urgensi' => $request->tingkat_urgensi,
                'deskripsi_kerusakan' => $request->deskripsi_kerusakan,
                'foto_kerusakan' => $fotoName,
                'status_ticket' => 'Open',
                'tgl_lapor' => now(),
            ]);

            // Catat log status awal
            TroubleshootLog::create([
                'id_ticket' => $wo->id_ticket,
                'status_lama' => 'None',
                'status_baru' => 'Open',
                'catatan' => 'Tiket laporan kerusakan berhasil dibuka oleh ' . $request->nama_pelapor_bebas,
                'diubah_oleh' => Auth::id(),
            ]);

            // Sync asset condition based on urgency
            $assetCondition = in_array($request->tingkat_urgensi, ['Tinggi', 'Darurat']) ? 'Rusak_Berat' : 'Rusak_Ringan';
            Aset::where('id_aset', $request->id_aset)->update(['status_kondisi' => $assetCondition]);

            DB::commit();

            return Redirect::route('workorder.index')->with('success', 'Work Order berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Terjadi kesalahan sistem saat membuat Work Order.');
        }
    }

    /**
     * Perbarui status Work Order (oleh Staf IPSRS)
     */
    public function updateStatus(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['Admin_IPSRS', 'Staf_IPSRS'])) {
            abort(403, 'Hanya Staf IPSRS yang dapat mengubah status Work Order.');
        }

        $request->validate([
            'status_ticket' => 'required|in:Open,Pengecekan,Dikerjakan,Menunggu Sign-Off,Closed',
            'catatan_status' => 'nullable|string|max:255',
        ]);

        $ticket = Troubleshoot::findOrFail($id);
        $statusLama = $ticket->status_ticket;
        $statusBaru = $request->status_ticket;

        try {
            DB::beginTransaction();

            $ticket->update([
                'status_ticket' => $statusBaru
            ]);

            // Sync asset condition based on work order status update
            if ($statusBaru === 'Closed') {
                Aset::where('id_aset', $ticket->id_aset)->update(['status_kondisi' => 'Baik']);
            } elseif (in_array($statusBaru, ['Pengecekan', 'Dikerjakan', 'Menunggu Sign-Off'])) {
                Aset::where('id_aset', $ticket->id_aset)->update(['status_kondisi' => 'Maintenance']);
            }

            TroubleshootLog::create([
                'id_ticket' => $ticket->id_ticket,
                'status_lama' => $statusLama,
                'status_baru' => $statusBaru,
                'catatan' => $request->catatan_status ?: 'Perubahan status tiket oleh ' . $user->nama_lengkap,
                'diubah_oleh' => $user->id_user,
            ]);

            DB::commit();

            return Redirect::route('workorder.index')->with('success', 'Status Work Order berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Gagal memperbarui status Work Order.');
        }
    }

    /**
     * Assign Teknisi Penanggungjawab
     */
    public function assignTeknisi(Request $request, $id)
    {
        $user = Auth::user();
        if (!in_array($user->role, ['Admin_IPSRS', 'Staf_IPSRS'])) {
            abort(403, 'Hanya Staf IPSRS yang dapat meng-assign teknisi.');
        }

        if (in_array($user->username, ['budi_ipsrs', 'hendra_ipsrs', 'agus_ipsrs'])) {
            abort(403, 'Akses ditolak. Teknisi lapangan tidak memiliki wewenang menugaskan teknisi.');
        }

        $request->validate([
            'id_teknisi_penanggungjawab' => 'required|integer|exists:m_user,id_user',
        ]);

        $ticket = Troubleshoot::findOrFail($id);
        $teknisi = User::findOrFail($request->id_teknisi_penanggungjawab);

        try {
            DB::beginTransaction();

            $ticket->update([
                'id_teknisi_penanggungjawab' => $request->id_teknisi_penanggungjawab
            ]);

            TroubleshootLog::create([
                'id_ticket' => $ticket->id_ticket,
                'status_lama' => $ticket->status_ticket,
                'status_baru' => $ticket->status_ticket,
                'catatan' => 'Teknisi ' . $teknisi->nama_lengkap . ' ditugaskan untuk menangani laporan.',
                'diubah_oleh' => $user->id_user,
            ]);

            DB::commit();

            return Redirect::route('workorder.index')->with('success', 'Teknisi penanggungjawab berhasil ditugaskan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Gagal menugaskan teknisi.');
        }
    }

    /**
     * Sign-Off Selesai (oleh Unit RS pelapor)
     */
    public function signOff(Request $request, $id)
    {
        $user = Auth::user();
        if (!$this->isUnit()) {
            abort(403, 'Hanya Unit yang dapat melakukan Sign-Off.');
        }

        $ticket = Troubleshoot::findOrFail($id);

        if ($ticket->id_user_pelapor != $user->id_user) {
            abort(403, 'Akses ditolak. Tiket ini dibuat oleh unit lain.');
        }

        if ($ticket->status_ticket !== 'Menunggu Sign-Off') {
            return Redirect::back()->with('error', 'Tiket belum siap untuk ditandatangani/selesai.');
        }

        $request->validate([
            'catatan_signoff' => 'nullable|string|max:255',
        ]);

        try {
            DB::beginTransaction();

            $ticket->update([
                'status_ticket' => 'Closed'
            ]);

            // Set asset condition back to 'Baik' upon successful sign-off
            Aset::where('id_aset', $ticket->id_aset)->update(['status_kondisi' => 'Baik']);

            TroubleshootLog::create([
                'id_ticket' => $ticket->id_ticket,
                'status_lama' => 'Menunggu Sign-Off',
                'status_baru' => 'Closed',
                'catatan' => $request->catatan_signoff ?: 'Tiket ditutup dan diverifikasi selesai oleh Unit RS',
                'diubah_oleh' => $user->id_user,
            ]);

            DB::commit();

            return Redirect::route('workorder.index')->with('success', 'Sign-Off berhasil. Tiket resmi ditutup.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Gagal memproses penutupan tiket.');
        }
    }
}
