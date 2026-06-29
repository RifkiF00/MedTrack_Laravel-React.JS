<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TrackingController extends Controller
{
    /**
     * Tampilkan Halaman Peta Pelacakan GPS Aset
     */
    public function map()
    {
        // Ambil semua aset yang memiliki titik koordinat GPS aktif (tidak null)
        $asets = Aset::with(['ruangan'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($aset) {
                return [
                    'id_aset' => $aset->id_aset,
                    'kode_label' => $aset->kode_label,
                    'nama_alat' => $aset->nama_alat,
                    'kategori_aset' => $aset->kategori_aset,
                    'status_kondisi' => $aset->status_kondisi,
                    'ruangan' => $aset->ruangan ? $aset->ruangan->nama_ruang : 'Tidak ada',
                    'latitude' => (float)$aset->latitude,
                    'longitude' => (float)$aset->longitude,
                ];
            });

        // Ambil semua ruangan yang memiliki koordinat GPS aktif
        $ruangans = \App\Models\Ruangan::with(['asets'])
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($ruangan) {
                return [
                    'id_ruang' => $ruangan->id_ruang,
                    'nama_ruang' => $ruangan->nama_ruang,
                    'kategori' => $ruangan->kategori,
                    'lokasi_gedung' => $ruangan->lokasi_gedung,
                    'latitude' => (float)$ruangan->latitude,
                    'longitude' => (float)$ruangan->longitude,
                    'total_aset' => $ruangan->asets->count(),
                    'asets_list' => $ruangan->asets->map(function ($aset) {
                        return [
                            'id_aset' => $aset->id_aset,
                            'kode_label' => $aset->kode_label,
                            'nama_alat' => $aset->nama_alat,
                            'status_kondisi' => $aset->status_kondisi,
                        ];
                    })->values()->all(),
                ];
            });

        return Inertia::render('Tracking/Map', [
            'asets' => $asets,
            'ruangans' => $ruangans,
        ]);
    }
}
