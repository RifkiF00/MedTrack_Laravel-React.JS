<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\Tracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ApiAsetController extends Controller
{
    /**
     * Mendapatkan detail aset berdasarkan QR Code
     */
    public function getDetailByQr($qr_code)
    {
        $aset = Aset::with(['ruangan'])
            ->where('kode_label', $qr_code)
            ->first();

        if (!$aset) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aset tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Aset berhasil ditemukan',
            'data' => [
                'id_aset' => $aset->id_aset,
                'kode_label' => $aset->kode_label,
                'nama_alat' => $aset->nama_alat,
                'kategori_aset' => $aset->kategori_aset,
                'merk' => $aset->merk,
                'model' => $aset->model,
                'serial_number' => $aset->serial_number,
                'status_kondisi' => $aset->status_kondisi,
                'ruangan' => $aset->ruangan ? $aset->ruangan->nama_ruang : 'Tidak ada',
                'id_ruang' => $aset->id_ruang_saat_ini,
                'latitude' => $aset->latitude,
                'longitude' => $aset->longitude,
            ]
        ], 200);
    }

    /**
     * Update lokasi GPS aset baru dari Scan QR Android
     */
    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'qr_code' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'id_ruang' => 'nullable|integer|exists:m_ruangan,id_ruang',
            'keterangan' => 'nullable|string|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Input tidak valid',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari aset berdasarkan QR Code
        $aset = Aset::where('kode_label', $request->qr_code)->first();

        if (!$aset) {
            return response()->json([
                'status' => 'error',
                'message' => 'Aset dengan QR Code tersebut tidak ditemukan'
            ], 404);
        }

        // Menggunakan Database Transaction untuk keamanan konsistensi data
        try {
            DB::beginTransaction();

            $user = $request->user(); // Dapatkan staf yang sedang login dari token

            // 1. Buat log entry baru di t_tracking
            $tracking = Tracking::create([
                'id_aset' => $aset->id_aset,
                'id_user' => $user ? $user->id_user : null,
                'id_ruang' => $request->id_ruang ?? $aset->id_ruang_saat_ini,
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'tgl_update' => now(),
                'keterangan' => $request->keterangan ?? 'Scan tracking via Android App'
            ]);

            // 2. Update status & lokasi terkini di table m_aset
            $aset->update([
                'latitude' => $request->latitude,
                'longitude' => $request->longitude,
                'id_ruang_saat_ini' => $request->id_ruang ?? $aset->id_ruang_saat_ini
            ]);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Lokasi aset berhasil diperbarui',
                'data' => [
                    'id_track' => $tracking->id_track,
                    'id_aset' => $aset->id_aset,
                    'kode_label' => $aset->kode_label,
                    'nama_alat' => $aset->nama_alat,
                    'latitude' => $aset->latitude,
                    'longitude' => $aset->longitude,
                    'id_ruang' => $aset->id_ruang_saat_ini
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'status' => 'error',
                'message' => 'Terjadi kesalahan sistem saat memperbarui lokasi',
                'error_detail' => $e->getMessage()
            ], 500);
        }
    }
}
