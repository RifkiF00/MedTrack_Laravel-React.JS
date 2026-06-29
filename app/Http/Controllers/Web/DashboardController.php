<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\Ruangan;
use App\Models\Troubleshoot;
use App\Models\Maintenance;
use App\Models\Mutasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $role = $user->role;
        $idRuang = $user->id_ruang;
        $userId = $user->id_user;

        // 1. Data Counter Umum (Filter berdasarkan ruangan jika role Unit_RS)
        $asetQuery = Aset::query();
        if ($role === 'Unit_RS' && $idRuang) {
            $asetQuery->where('id_ruang_saat_ini', $idRuang);
        }

        $totalAset = (clone $asetQuery)->count();
        $totalMedis = (clone $asetQuery)->where('kategori_aset', 'Medis')->count();
        $totalSarpras = (clone $asetQuery)->where('kategori_aset', 'Sarpras')->count();
        $totalIT = (clone $asetQuery)->where('kategori_aset', 'IT')->count();

        $baik = (clone $asetQuery)->where('status_kondisi', 'Baik')->count();
        $rusakRingan = (clone $asetQuery)->where('status_kondisi', 'Rusak_Ringan')->count();
        $rusakBerat = (clone $asetQuery)->where('status_kondisi', 'Rusak_Berat')->count();
        $maintenance = (clone $asetQuery)->where('status_kondisi', 'Maintenance')->count();
        $gudang = (clone $asetQuery)->where('status_kondisi', 'Gudang')->count();

        // 2. Data Statistik Ruangan (Untuk Chart)
        $ruanganStatsQuery = Ruangan::select('m_ruangan.nama_ruang', DB::raw('COUNT(m_aset.id_aset) as total_aset'))
            ->leftJoin('m_aset', 'm_ruangan.id_ruang', '=', 'm_aset.id_ruang_saat_ini');
        
        if ($role === 'Unit_RS' && $idRuang) {
            $ruanganStatsQuery->where('m_ruangan.id_ruang', $idRuang);
        }

        $ruanganStats = $ruanganStatsQuery->groupBy('m_ruangan.id_ruang', 'm_ruangan.nama_ruang')
            ->orderBy('total_aset', 'desc')
            ->limit(8)
            ->get();

        $ruanganLabels = $ruanganStats->pluck('nama_ruang')->toArray();
        $ruanganTotals = $ruanganStats->pluck('total_aset')->map(fn($val) => (int)$val)->toArray();

        // 3. Notifikasi Dinamis Berdasarkan Role
        $notifications = [];

        if ($role === 'Admin_IPSRS' || $role === 'Staf_IPSRS') {
            // WO yang masih open/proses
            $woOpen = Troubleshoot::with('aset')
                ->whereIn('status_ticket', ['Open', 'Pengecekan', 'Dikerjakan'])
                ->orderBy('tgl_lapor', 'desc')
                ->limit(5)
                ->get();

            foreach ($woOpen as $wo) {
                $notifications[] = [
                    'id' => 'wo_' . $wo->id_ticket,
                    'type' => 'wo_open',
                    'icon' => '🔴',
                    'title' => 'Work Order Baru',
                    'message' => ($wo->aset ? $wo->aset->kode_label : 'N/A') . ' - ' . $wo->deskripsi_kerusakan,
                    'color' => '#ef4444',
                ];
            }

            // Aset yang rusak berat
            $asetRusak = Aset::where('status_kondisi', 'Rusak_Berat')
                ->limit(5)
                ->get();

            foreach ($asetRusak as $aset) {
                $notifications[] = [
                    'id' => 'aset_' . $aset->id_aset,
                    'type' => 'aset_rusak',
                    'icon' => '⚠️',
                    'title' => 'Aset Rusak Berat',
                    'message' => $aset->kode_label . ' - ' . $aset->nama_alat,
                    'color' => '#f97316',
                ];
            }
        } elseif ($role === 'Staf_Logistik') {
            // Recent Assets
            $recentAset = Aset::orderBy('created_at', 'desc')
                ->limit(5)
                ->get();

            foreach ($recentAset as $aset) {
                $notifications[] = [
                    'id' => 'recent_' . $aset->id_aset,
                    'type' => 'aset_baru',
                    'icon' => '📦',
                    'title' => 'Aset Baru Didaftar',
                    'message' => $aset->kode_label . ' - ' . $aset->nama_alat,
                    'color' => '#10b981',
                ];
            }
        } elseif ($role === 'Unit_RS') {
            // WO milik pelapor yang belum resolved
            $woUnresolved = Troubleshoot::with('aset')
                ->where('id_user_pelapor', $userId)
                ->where('status_ticket', '!=', 'Closed')
                ->limit(5)
                ->get();

            foreach ($woUnresolved as $wo) {
                $isSignOff = $wo->status_ticket === 'Menunggu Sign-Off';
                $notifications[] = [
                    'id' => 'unresolved_' . $wo->id_ticket,
                    'type' => 'wo_unresolved',
                    'icon' => $isSignOff ? '✍️' : '🔔',
                    'title' => $isSignOff ? 'Butuh Sign-Off (Selesai)' : 'Progress WO: ' . $wo->status_ticket,
                    'message' => ($wo->aset ? $wo->aset->nama_alat : 'Aset') . ' - ' . $wo->deskripsi_kerusakan,
                    'color' => $isSignOff ? '#10b981' : '#f59e0b',
                ];
            }
        }

        return Inertia::render('Dashboard', [
            'role' => $role,
            'nama' => $user->nama_lengkap,
            'stats' => [
                'total_aset' => $totalAset,
                'total_medis' => $totalMedis,
                'total_sarpras' => $totalSarpras,
                'total_it' => $totalIT,
                'baik' => $baik,
                'rusak_ringan' => $rusakRingan,
                'rusak_berat' => $rusakBerat,
                'maintenance' => $maintenance,
                'gudang' => $gudang,
                'total_ruangan' => \App\Models\Ruangan::count(),
                'total_mutasi' => \App\Models\Mutasi::count(),
                'total_wo_pending' => \App\Models\Troubleshoot::where('status_ticket', 'Open')->count(),
            ],
            'chart' => [
                'labels' => $ruanganLabels,
                'datasets' => [
                    [
                        'label' => 'Jumlah Aset',
                        'data' => $ruanganTotals,
                        'backgroundColor' => '#4f46e5',
                    ]
                ]
            ],
            'dashboardNotifications' => $notifications,
            'dashboardNotificationCount' => count($notifications),
        ]);
    }

    /**
     * Reschedule item kalibrasi / maintenance dari calendar sidebar
     */
    public function rescheduleCalendar(Request $request)
    {
        $request->validate([
            'type' => 'required|in:maintenance,calibration',
            'id' => 'required|integer',
            'new_date' => 'required|date',
        ]);

        $type = $request->input('type');
        $id = $request->input('id');
        $newDate = $request->input('new_date');

        if ($type === 'maintenance') {
            $log = \App\Models\PemeliharaanLog::findOrFail($id);
            $log->update(['tgl_rencana' => $newDate]);
        } else {
            $aset = \App\Models\Aset::findOrFail($id);
            $aset->update([
                'tgl_kalibrasi_terakhir' => $newDate,
                'tgl_kadaluarsa_sertif' => $newDate
            ]);
        }

        return redirect()->back()->with('success', 'Jadwal berhasil diperbarui.');
    }
}
