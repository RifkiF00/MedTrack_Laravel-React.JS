<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): string|null
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'upcomingEvents' => function () use ($request) {
                if (!$request->user()) return [];
                
                // Get maintenance logs
                $maintenanceLogs = \App\Models\PemeliharaanLog::with('pemeliharaan')
                    ->where('status_pelaksanaan', 'Terjadwal')
                    ->orderBy('tgl_rencana', 'asc')
                    ->limit(20)
                    ->get()
                    ->map(function ($log) {
                        return [
                            'type' => 'maintenance',
                            'id' => $log->id_log,
                            'title' => $log->pemeliharaan ? $log->pemeliharaan->nama_item : 'Rutin PM',
                            'date' => $log->tgl_rencana ? $log->tgl_rencana->format('Y-m-d') : null,
                            'location' => $log->pemeliharaan ? $log->pemeliharaan->lokasi : 'RS Hasna Medika',
                        ];
                    });

                $calibrations = \App\Models\Aset::with('ruangan')
                    ->whereNotNull('tgl_kalibrasi_terakhir')
                    ->orderBy('tgl_kalibrasi_terakhir', 'asc')
                    ->limit(150)
                    ->get()
                    ->map(function ($aset) {
                        return [
                            'type' => 'calibration',
                            'id' => $aset->id_aset,
                            'title' => 'Kalibrasi ' . $aset->nama_alat,
                            'date' => $aset->tgl_kalibrasi_terakhir ? $aset->tgl_kalibrasi_terakhir->format('Y-m-d') : null,
                            'location' => $aset->ruangan ? $aset->ruangan->nama_ruang : 'RS Hasna Medika',
                            'kode_label' => $aset->kode_label,
                        ];
                    });

                return $maintenanceLogs->concat($calibrations)->values()->all();
            },
            'upcomingCalibrations' => function () use ($request) {
                if (!$request->user()) return [];
                
                return \App\Models\Aset::with('ruangan')
                    ->whereNotNull('tgl_kadaluarsa_sertif')
                    ->where('tgl_kadaluarsa_sertif', '>=', now()->startOfDay()->format('Y-m-d'))
                    ->orderBy('tgl_kadaluarsa_sertif', 'asc')
                    ->limit(10)
                    ->get()
                    ->map(function ($aset) {
                        $diffInDays = now()->startOfDay()->diffInDays($aset->tgl_kadaluarsa_sertif, false);
                        return [
                            'id_aset' => $aset->id_aset,
                            'kode_label' => $aset->kode_label,
                            'nama_alat' => $aset->nama_alat,
                            'tgl_kadaluarsa_sertif' => $aset->tgl_kadaluarsa_sertif->format('Y-m-d'),
                            'tgl_kadaluarsa_formatted' => $aset->tgl_kadaluarsa_sertif->format('d M Y'),
                            'nama_ruang' => $aset->ruangan ? $aset->ruangan->nama_ruang : 'RS Hasna Medika',
                            'sisa_hari' => $diffInDays,
                        ];
                    })->values()->all();
            },
            'notifications' => function () use ($request) {
                $user = $request->user();
                if (!$user) return [];
                
                $role = $user->role;
                $notifications = [];
                
                if ($role === 'Admin_IPSRS' || $role === 'Staf_IPSRS') {
                    // 1. Scheduled PM
                    $scheduledCount = \App\Models\PemeliharaanLog::where('status_pelaksanaan', 'Terjadwal')->count();
                    if ($scheduledCount > 0) {
                        $notifications[] = [
                            'id' => 'pm_scheduled',
                            'text' => "Ada {$scheduledCount} jadwal pemeliharaan preventif mendesak.",
                            'link' => route('maintenance.index'),
                            'type' => 'warning'
                        ];
                    }
                    
                    // 2. Pending Mutasi
                    $pendingMutasiCount = \App\Models\Mutasi::where('status_mutasi', 'Menunggu_Verifikasi')->count();
                    if ($pendingMutasiCount > 0) {
                        $notifications[] = [
                            'id' => 'mutasi_pending',
                            'text' => "Ada {$pendingMutasiCount} permohonan mutasi aset menunggu persetujuan.",
                            'link' => route('mutasi.index'),
                            'type' => 'info'
                        ];
                    }
                    
                    // 3. Open WO
                    $openWOCount = \App\Models\Troubleshoot::where('status_ticket', 'Terbuka')->count();
                    if ($openWOCount > 0) {
                        $notifications[] = [
                            'id' => 'wo_open',
                            'text' => "Ada {$openWOCount} laporan kerusakan baru belum ditugaskan.",
                            'link' => route('workorder.index'),
                            'type' => 'danger'
                        ];
                    }
                } elseif ($role === 'Staf_Logistik') {
                    // Expired Calibration
                    $expiredCalCount = \App\Models\Aset::whereNotNull('tgl_kadaluarsa_sertif')
                        ->where('tgl_kadaluarsa_sertif', '<', now()->startOfDay()->format('Y-m-d'))
                        ->count();
                    if ($expiredCalCount > 0) {
                        $notifications[] = [
                            'id' => 'cal_expired',
                            'text' => "Ada {$expiredCalCount} alat medis yang sertifikat kalibrasinya kadaluarsa.",
                            'link' => route('aset.index'),
                            'type' => 'danger'
                        ];
                    }
                } elseif ($role === 'Unit_RS') {
                    // Open WO in my room
                    $myOpenWO = \App\Models\Troubleshoot::where('status_ticket', '!=', 'Closed')
                        ->where('id_user_pelapor', $user->id_user)
                        ->count();
                    if ($myOpenWO > 0) {
                        $notifications[] = [
                            'id' => 'my_wo_open',
                            'text' => "Ada {$myOpenWO} laporan kerusakan di ruangan Anda yang sedang diproses.",
                            'link' => route('workorder.index'),
                            'type' => 'warning'
                        ];
                    }
                }
                
                return $notifications;
            }
        ];
    }
}
