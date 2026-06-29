<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\Pemeliharaan;
use App\Models\PemeliharaanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class MaintenanceController extends Controller
{
    private function checkAccess()
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['Admin_IPSRS', 'Staf_IPSRS'])) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Staf IPSRS.');
        }
    }

    /**
     * Halaman Utama Preventive Maintenance
     */
    public function index()
    {
        $this->checkAccess();

        $today = now()->format('Y-m-d');
        $month = now()->format('m');
        $year = now()->format('Y');

        // Ambil semua log terjadwal (pending)
        $scheduledItems = PemeliharaanLog::with('pemeliharaan')
            ->where('status_pelaksanaan', 'Terjadwal')
            ->orderBy('tgl_rencana', 'asc')
            ->get()
            ->map(function ($log) {
                return [
                    'id_log' => $log->id_log,
                    'id_pemeliharaan' => $log->id_pemeliharaan,
                    'nama_item' => $log->pemeliharaan ? $log->pemeliharaan->nama_item : 'N/A',
                    'frekuensi' => $log->pemeliharaan ? $log->pemeliharaan->frekuensi : 'N/A',
                    'lokasi' => $log->pemeliharaan ? $log->pemeliharaan->lokasi : 'N/A',
                    'tgl_rencana' => $log->tgl_rencana ? $log->tgl_rencana->format('Y-m-d') : null,
                    'status_pelaksanaan' => $log->status_pelaksanaan,
                ];
            });

        // Ambil log yang terselesaikan
        $completedLogs = PemeliharaanLog::with(['pemeliharaan', 'pelaksana'])
            ->where('status_pelaksanaan', 'Terselesaikan')
            ->orderBy('tgl_pelaksanaan', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($log) {
                return [
                    'id_log' => $log->id_log,
                    'nama_item' => $log->pemeliharaan ? $log->pemeliharaan->nama_item : 'N/A',
                    'frekuensi' => $log->pemeliharaan ? $log->pemeliharaan->frekuensi : 'N/A',
                    'pelaksana' => $log->pelaksana ? $log->pelaksana->nama_lengkap : 'N/A',
                    'tgl_pelaksanaan' => $log->tgl_pelaksanaan ? $log->tgl_pelaksanaan->format('Y-m-d H:i') : null,
                    'tgl_pelaksanaan_raw' => $log->tgl_pelaksanaan ? $log->tgl_pelaksanaan->format('Y-m-d') : null,
                    'status_pelaksanaan' => $log->status_pelaksanaan,
                    'hasil_pengecekan' => $log->hasil_pengecekan,
                    'kondisi_laporan' => $log->kondisi_laporan,
                    'catatan_khusus' => $log->catatan_khusus,
                ];
            });

        // Statistik
        $totalSchedules = Pemeliharaan::where('status', 'Aktif')->count();
        $completedCount = PemeliharaanLog::where('status_pelaksanaan', 'Terselesaikan')
            ->whereMonth('tgl_pelaksanaan', $month)
            ->whereYear('tgl_pelaksanaan', $year)
            ->count();
        $pendingCount = PemeliharaanLog::where('status_pelaksanaan', 'Terjadwal')->count();

        // Master List PM Items
        $masterItems = Pemeliharaan::where('status', 'Aktif')->get()->map(function ($p) {
            return [
                'id_pemeliharaan' => $p->id_pemeliharaan,
                'nama_item' => $p->nama_item,
                'lokasi' => $p->lokasi,
                'frekuensi' => $p->frekuensi,
                'pic_penanggung_jawab' => $p->pic_penanggung_jawab,
                'deskripsi' => $p->deskripsi,
                'catatan' => $p->catatan,
            ];
        });

        return Inertia::render('Maintenance/Index', [
            'scheduledItems' => $scheduledItems,
            'completedLogs' => $completedLogs,
            'masterItems' => $masterItems,
            'stats' => [
                'total_master' => $totalSchedules,
                'completed_month' => $completedCount,
                'pending_schedules' => $pendingCount
            ]
        ]);
    }

    /**
     * Halaman Tambah Jadwal Pemeliharaan Baru
     */
    public function create()
    {
        $this->checkAccess();

        // Mengambil seluruh list aset untuk dijadikan item pemeliharaan
        $asets = Aset::orderBy('nama_alat', 'asc')->get()->map(function ($a) {
            return [
                'id_aset' => $a->id_aset,
                'kode_label' => $a->kode_label,
                'nama_alat' => $a->nama_alat,
                'lokasi' => $a->ruangan ? $a->ruangan->nama_ruang : 'Tidak diketahui'
            ];
        });

        return Inertia::render('Maintenance/Create', [
            'asets' => $asets
        ]);
    }

    /**
     * Simpan Master Jadwal Pemeliharaan Baru
     */
    public function store(Request $request)
    {
        $this->checkAccess();

        $request->validate([
            'nama_item' => 'required|string|max:120',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'required|string|max:100',
            'frekuensi' => 'required|in:Harian,2x_Harian,3x_Harian,Mingguan,Bulanan,3_Bulanan,6_Bulanan,Tahunan',
            'catatan' => 'nullable|string',
            'tgl_rencana_awal' => 'required|date' // Tanggal rencana awal eksekusi
        ]);

        try {
            DB::beginTransaction();

            // 1. Buat master pemeliharaan
            $pemeliharaan = Pemeliharaan::create([
                'nama_item' => $request->nama_item,
                'deskripsi' => $request->deskripsi,
                'lokasi' => $request->lokasi,
                'frekuensi' => $request->frekuensi,
                'pic_penanggung_jawab' => Auth::user()->nama_lengkap,
                'catatan' => $request->catatan,
                'status' => 'Aktif'
            ]);

            // 2. Buat log awal dengan status 'Terjadwal'
            PemeliharaanLog::create([
                'id_pemeliharaan' => $pemeliharaan->id_pemeliharaan,
                'tgl_rencana' => $request->tgl_rencana_awal,
                'status_pelaksanaan' => 'Terjadwal',
                'kondisi_laporan' => 'Normal'
            ]);

            DB::commit();

            return Redirect::route('maintenance.index')->with('success', 'Master jadwal pemeliharaan rutin berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Terjadi kesalahan sistem saat membuat jadwal pemeliharaan.');
        }
    }

    /**
     * Log Pelaksanaan / Penyelesaian Tugas Pemeliharaan
     */
    public function log(Request $request)
    {
        $this->checkAccess();

        $request->validate([
            'id_log' => 'nullable|integer|exists:t_pemeliharaan_log,id_log',
            'id_pemeliharaan' => 'required_without:id_log|integer|exists:m_pemeliharaan,id_pemeliharaan',
            'tgl_rencana' => 'nullable|date',
            'status_pelaksanaan' => 'nullable|in:Terjadwal,Terselesaikan,Tertunda,Dibatalkan',
            'hasil_pengecekan' => 'required|string',
            'kondisi_laporan' => 'required|in:Normal,Butuh Perbaikan,Perlu Perbaikan,Rusak,Penggantian Part',
            'catatan_khusus' => 'nullable|string',
            'tgl_rencana_berikutnya' => 'nullable|date'
        ]);

        try {
            DB::beginTransaction();

            if ($request->filled('id_log')) {
                // Update log yang ada menjadi Terselesaikan
                $log = PemeliharaanLog::findOrFail($request->id_log);
                $log->update([
                    'id_user_pelaksana' => Auth::id(),
                    'tgl_pelaksanaan' => now(),
                    'status_pelaksanaan' => $request->input('status_pelaksanaan', 'Terselesaikan'),
                    'hasil_pengecekan' => $request->hasil_pengecekan,
                    'kondisi_laporan' => $request->kondisi_laporan,
                    'catatan_khusus' => $request->catatan_khusus
                ]);
            } else {
                // Buat log baru dari scratch (seperti di UTS native)
                $log = PemeliharaanLog::create([
                    'id_pemeliharaan' => $request->id_pemeliharaan,
                    'id_user_pelaksana' => Auth::id(),
                    'tgl_rencana' => $request->input('tgl_rencana', now()->format('Y-m-d')),
                    'tgl_pelaksanaan' => $request->input('status_pelaksanaan', 'Terselesaikan') === 'Terselesaikan' ? now() : null,
                    'status_pelaksanaan' => $request->input('status_pelaksanaan', 'Terselesaikan'),
                    'hasil_pengecekan' => $request->hasil_pengecekan,
                    'kondisi_laporan' => $request->kondisi_laporan,
                    'catatan_khusus' => $request->catatan_khusus
                ]);
            }

            // Jika ada tanggal rencana berikutnya, otomatis jadwalkan ulang log baru
            if ($request->tgl_rencana_berikutnya) {
                PemeliharaanLog::create([
                    'id_pemeliharaan' => $log->id_pemeliharaan,
                    'tgl_rencana' => $request->tgl_rencana_berikutnya,
                    'status_pelaksanaan' => 'Terjadwal',
                    'kondisi_laporan' => 'Normal'
                ]);
            }

            DB::commit();

            return Redirect::route('maintenance.index')->with('success', 'Log pelaksanaan pemeliharaan berhasil disimpan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return Redirect::back()->with('error', 'Terjadi kesalahan sistem saat menyimpan log pemeliharaan.');
        }
    }

    /**
     * Perbarui data master pemeliharaan
     */
    public function updateMaster(Request $request, $id)
    {
        $this->checkAccess();

        $request->validate([
            'nama_item' => 'required|string|max:120',
            'deskripsi' => 'nullable|string',
            'lokasi' => 'required|string|max:100',
            'frekuensi' => 'required|in:Harian,2x_Harian,3x_Harian,Mingguan,Bulanan,3_Bulanan,6_Bulanan,Tahunan',
            'catatan' => 'nullable|string',
        ]);

        $item = Pemeliharaan::findOrFail($id);
        $item->update($request->all());

        return Redirect::route('maintenance.index')->with('success', 'Master pemeliharaan rutin berhasil diperbarui.');
    }

    /**
     * Hapus master pemeliharaan beserta log-log-nya
     */
    public function deleteMaster($id)
    {
        $this->checkAccess();

        $item = Pemeliharaan::findOrFail($id);
        
        // Hapus log pemeliharaan terkait
        $item->logs()->delete();
        $item->delete();

        return Redirect::route('maintenance.index')->with('success', 'Master pemeliharaan rutin berhasil dihapus.');
    }

    /**
     * Perbarui log pelaksanaan / pengecekan
     */
    public function updateLog(Request $request, $id)
    {
        $this->checkAccess();

        $request->validate([
            'hasil_pengecekan' => 'required|string',
            'kondisi_laporan' => 'required|in:Normal,Butuh Perbaikan,Rusak',
            'catatan_khusus' => 'nullable|string',
            'tgl_pelaksanaan' => 'required|date'
        ]);

        $log = PemeliharaanLog::findOrFail($id);
        $log->update([
            'hasil_pengecekan' => $request->hasil_pengecekan,
            'kondisi_laporan' => $request->kondisi_laporan,
            'catatan_khusus' => $request->catatan_khusus,
            'tgl_pelaksanaan' => $request->tgl_pelaksanaan
        ]);

        return Redirect::route('maintenance.index')->with('success', 'Log pengecekan berhasil diperbarui.');
    }

    /**
     * Hapus log pelaksanaan / pengecekan
     */
    public function deleteLog($id)
    {
        $this->checkAccess();

        $log = PemeliharaanLog::findOrFail($id);
        $log->delete();

        return Redirect::route('maintenance.index')->with('success', 'Log pengecekan berhasil dihapus.');
    }
}

