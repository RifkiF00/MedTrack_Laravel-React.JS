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
            }
        ];
    }
}
