<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Aset;
use App\Models\Ruangan;
use App\Models\User;
use App\Models\PemeliharaanLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DokumenController extends Controller
{
    private function checkAccess()
    {
        $role = Auth::user()->role;
        if (!in_array($role, ['Admin_IPSRS', 'Staf_IPSRS', 'Staf_Logistik'])) {
            abort(403, 'Akses ditolak. Fitur ini hanya untuk Staf IPSRS dan Staf Logistik.');
        }
    }

    public function index()
    {
        $this->checkAccess();

        return Inertia::render('Dokumen/Index', [
            'role' => Auth::user()->role
        ]);
    }

    public function exportPdf($type)
    {
        $this->checkAccess();

        $logoPath = '/uploads/assets/logo-rs.png';
        $title = '';
        $htmlContent = '';

        switch ($type) {
            case 'total_aset':
                $title = 'LAPORAN TOTAL ASET';
                $asetList = Aset::with('ruangan')->get();
                $headers = ['Kode Label', 'Nama Aset', 'Kategori', 'Ruangan', 'Kondisi'];
                $rows = [];
                foreach ($asetList as $item) {
                    $rows[] = [
                        $item->kode_label ?? '-',
                        $item->nama_alat ?? '-',
                        $item->kategori_aset ?? '-',
                        $item->ruangan ? $item->ruangan->nama_ruang : '-',
                        str_replace('_', ' ', $item->status_kondisi ?? '-')
                    ];
                }
                $htmlContent .= '<p style="color: #4b5563; line-height: 1.6; margin: 10px 0;">Total Keseluruhan Aset: <strong>' . count($asetList) . '</strong> unit</p>';
                $htmlContent .= $this->buildTable($headers, $rows);
                break;

            case 'ruangan_aset':
                $title = 'LAPORAN RUANGAN DAN ASET';
                $ruangans = Ruangan::withCount('aset')->get();
                $headers = ['Ruangan', 'Kategori', 'Jumlah Aset'];
                $rows = [];
                $totalAset = 0;
                foreach ($ruangans as $item) {
                    $totalAset += $item->aset_count;
                    $rows[] = [
                        $item->nama_ruang,
                        $item->kategori ?? '-',
                        $item->aset_count . ' Aset'
                    ];
                }
                $rows[] = [
                    '<strong>TOTAL KESELURUHAN</strong>',
                    '',
                    '<strong>' . $totalAset . ' Aset</strong>'
                ];
                $htmlContent .= '<p style="color: #4b5563; line-height: 1.6; margin: 10px 0;">Total Ruangan Terdaftar: <strong>' . count($ruangans) . '</strong> ruangan</p>';
                $htmlContent .= $this->buildTable($headers, $rows);
                break;

            case 'sdm_staff':
                $title = 'LAPORAN SDM DAN STAFF';
                $staffList = User::with('ruangan')->get();
                $headers = ['Nama', 'Role', 'Unit/Ruangan', 'No. HP'];
                $rows = [];
                foreach ($staffList as $staff) {
                    $rows[] = [
                        $staff->nama_lengkap,
                        str_replace('_', ' ', $staff->role ?? '-'),
                        $staff->ruangan ? $staff->ruangan->nama_ruang : '-',
                        $staff->no_hp ?? '-'
                    ];
                }
                $htmlContent .= '<p style="color: #4b5563; line-height: 1.6; margin: 10px 0;">Total SDM & Staff Aktif: <strong>' . count($staffList) . '</strong> orang</p>';
                $htmlContent .= $this->buildTable($headers, $rows);
                break;

            case 'maintenance':
                $title = 'LAPORAN MAINTENANCE BULANAN - ' . date('F Y');
                $bulan = date('m');
                $tahun = date('Y');
                $maintenance = PemeliharaanLog::with(['pemeliharaan', 'pelaksana'])
                    ->whereMonth('tgl_rencana', $bulan)
                    ->whereYear('tgl_rencana', $tahun)
                    ->get();

                $headers = ['Item/Aset', 'Pelaksana', 'Tanggal Rencana', 'Status'];
                $rows = [];
                $selesai = 0;
                foreach ($maintenance as $item) {
                    if ($item->status_pelaksanaan == 'Terselesaikan') {
                        $selesai++;
                    }
                    $rows[] = [
                        $item->pemeliharaan ? $item->pemeliharaan->nama_item : 'N/A',
                        $item->pelaksana ? $item->pelaksana->nama_lengkap : 'N/A',
                        $item->tgl_rencana ? $item->tgl_rencana->format('d/m/Y') : '-',
                        $item->status_pelaksanaan ?? '-'
                    ];
                }
                $htmlContent .= '<p style="color: #4b5563; line-height: 1.6; margin: 10px 0;">Total Jadwal Maintenance Bulan Ini: <strong>' . count($maintenance) . '</strong> jadwal (Selesai: ' . $selesai . ')</p>';
                $htmlContent .= $this->buildTable($headers, $rows);
                break;

            default:
                return redirect()->route('dokumen.index');
        }

        $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>' . $title . '</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 0; padding: 20px; color: #333; }
        @media print { body { margin: 0; padding: 0; } }
    </style>
</head>
<body>
    <div style="padding: 10px 20px 20px 20px;">
        <table style="width: 100%; padding-bottom: 5px;">
            <tr>
                <td style="width: 140px; text-align: left; vertical-align: top; padding-top: 5px;">
                    <img src="' . $logoPath . '" alt="Logo RS" style="width: 120px; height: auto; display: block;">
                </td>
                <td style="text-align: center; vertical-align: top;">
                    <h1 style="margin: 0; padding: 0; color: #000; font-size: 22px; font-weight: bold; font-family: \'Times New Roman\', Times, serif; text-transform: uppercase;">RUMAH SAKIT HASNA MEDIKA KUNINGAN</h1>
                    <p style="margin: 5px 0 0; padding: 0; color: #000; font-size: 14px; font-family: \'Times New Roman\', Times, serif;">Jl. Raya Cigugur Lingkungan Manis RT.26/RW.09</p>
                    <p style="margin: 2px 0 0; padding: 0; color: #000; font-size: 14px; font-family: \'Times New Roman\', Times, serif;">Kecamatan Kuningan, Kabupaten Kuningan, Jawa Barat 45511</p>
                    <p style="margin: 2px 0 0; padding: 0; color: #000; font-size: 14px; font-family: \'Times New Roman\', Times, serif;">Telepon: (0232) 8900000 | Email: info@hasnamedikakuningan.com</p>
                </td>
                <td style="width: 140px;"></td>
            </tr>
        </table>
        <hr style="border: 0; border-top: 3px solid #000; margin: 0;">
        <hr style="border: 0; border-top: 1px solid #000; margin: 2px 0 20px 0;">
        <h3 style="text-align: center; color: #1f2937; margin-top: 10px; margin-bottom: 5px; font-size: 16px; text-transform: uppercase; text-decoration: underline;">' . $title . '</h3>
        <p style="text-align: center; color: #6b7280; margin-top: 0; margin-bottom: 25px; font-size: 12px;">Dicetak pada: ' . date('d F Y, H:i') . ' WIB</p>
        <div style="margin-top: 20px;">
            ' . $htmlContent . '
        </div>
    </div>
    <script>
        window.onload = function() { window.print(); };
    </script>
</body>
</html>';

        return response($html)->header('Content-Type', 'text/html; charset=utf-8');
    }

    private function buildTable($headers, $rows)
    {
        $html = '<table style="width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 13px;">';
        $html .= '<thead><tr style="background: #f3f4f6; border-bottom: 2px solid #d1d5db;">';
        foreach ($headers as $header) {
            $html .= '<th style="padding: 10px; text-align: left; font-weight: 600; color: #1f2937;">' . $header . '</th>';
        }
        $html .= '</tr></thead>';
        $html .= '<tbody>';
        foreach ($rows as $row) {
            $html .= '<tr style="border-bottom: 1px solid #e5e7eb;">';
            foreach ($row as $cell) {
                $html .= '<td style="padding: 8px; color: #4b5563;">' . $cell . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;
    }
}
