<?php

class Dashboard extends Controller {

    private function guard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }
    }

    public function index() {
        $this->guard();

        $asetModel = $this->model('Aset_model');
        $workOrderModel = $this->model('WorkOrder_model');
        $maintenanceModel = $this->model('Maintenance_model');
        $mutasiModel = $this->model('Mutasi_model');

        $role = $_SESSION['role'] ?? 'User';
        $userId = $_SESSION['user_id'] ?? null;
        $nama = $_SESSION['nama_lengkap'] ?? 'User';
        $idRuang = $_SESSION['id_ruang'] ?? null;

        $data['role'] = $role;
        $data['nama'] = $nama;

        // Default
        $data['total_aset'] = 0;
        $data['total_medis'] = 0;
        $data['total_sarpras'] = 0;
        $data['total_it'] = 0;

        $data['baik'] = 0;
        $data['rusak_ringan'] = 0;
        $data['rusak_berat'] = 0;
        $data['maintenance'] = 0;
        $data['gudang'] = 0;

        $data['ruangan_labels'] = [];
        $data['ruangan_totals'] = [];

        // SIDEBAR DATA - Initialize empty
        $data['sidebar_data'] = [];

        // CALENDAR DATA - Get week schedules
        $data['jadwal_minggu_maintenance'] = $maintenanceModel->getJadwalMingguIni();
        $data['jadwal_minggu_wo'] = $workOrderModel->getWorkOrdersMingguIni();

        // SCHEDULE DATA - Get today's schedule details
        $today = date('Y-m-d');
        $data['jadwal_hari_ini_maintenance'] = $maintenanceModel->getMaintenanceByDate($today);
        $data['jadwal_hari_ini_wo'] = $workOrderModel->getWorkOrdersByDate($today);

        // NOTIFICATION DATA
        $data['notifications'] = [];
        $data['notification_count'] = 0;

        // Get notifications based on role
        if ($role === 'Staf_IPSRS') {
            $wo_open = $workOrderModel->getWorkOrderOpen(10);
            $aset_rusak = $asetModel->getAsetRusak();
            $maintenance_mendekati = $maintenanceModel->getKalibrasiMendekati(5);
            $kalibrasi_mendekati = $maintenanceModel->getKalibrasiMendekatiAset(5);

            $notifications = [];
            foreach ($wo_open as $wo) {
                $notifications[] = [
                    'type' => 'wo_open',
                    'icon' => '🔴',
                    'title' => 'Work Order Baru',
                    'message' => $wo->kode_label . ' - ' . ($wo->nama_alat ?? 'Equipment'),
                    'color' => '#cd1601',
                    'action_url' => BASEURL . '/aset/detail/' . $wo->id_aset
                ];
            }
            foreach (array_slice($aset_rusak, 0, 7) as $aset) {
                $notifications[] = [
                    'type' => 'aset_rusak',
                    'icon' => '⚠️',
                    'title' => 'Aset Rusak',
                    'message' => $aset->kode_label . ' - ' . $aset->nama_alat,
                    'color' => '#f97316',
                    'action_url' => BASEURL . '/aset/detail/' . $aset->id_aset
                ];
            }
            foreach (array_slice($maintenance_mendekati, 0, 2) as $m) {
                $notifications[] = [
                    'type' => 'maintenance',
                    'icon' => '📅',
                    'title' => 'Maintenance Mendekati',
                    'message' => $m->nama_item,
                    'color' => '#3b82f6',
                    'action_url' => BASEURL . '/maintenance'
                ];
            }
            foreach (array_slice($kalibrasi_mendekati, 0, 2) as $aset) {
                $notifications[] = [
                    'type' => 'kalibrasi_mendekati',
                    'icon' => '📋',
                    'title' => 'Jadwal Kalibrasi Mendekati',
                    'message' => $aset->kode_label . ' - ' . $aset->nama_alat,
                    'color' => '#f59e0b',
                    'action_url' => BASEURL . '/aset/edit/' . $aset->id_aset . '#kalibrasi-section'
                ];
            }
            $data['notifications'] = array_slice($notifications, 0, 10);
            $data['notification_count'] = count($notifications);
        } elseif ($role === 'Staf_Logistik') {
            $aset_recent = $asetModel->getRecentAset(5);
            $aset_perlu_pengadaan = $asetModel->getAsetPerluPengadaan();

            $notifications = [];
            foreach (array_slice($aset_recent, 0, 3) as $aset) {
                $notifications[] = [
                    'type' => 'aset_baru',
                    'icon' => '📦',
                    'title' => 'Aset Baru Ditambahkan',
                    'message' => $aset->kode_label . ' - ' . $aset->nama_alat,
                    'color' => '#10b981',
                    'action_url' => BASEURL . '/aset/detail/' . $aset->id_aset
                ];
            }
            foreach (array_slice($aset_perlu_pengadaan, 0, 3) as $aset) {
                $notifications[] = [
                    'type' => 'pengadaan',
                    'icon' => '🛒',
                    'title' => 'Perlu Pengadaan',
                    'message' => $aset->kode_label . ' - ' . $aset->status_kondisi,
                    'color' => '#ef4444',
                    'action_url' => BASEURL . '/aset'
                ];
            }
            $data['notifications'] = array_slice($notifications, 0, 10);
            $data['notification_count'] = count($notifications);
        } elseif ($role === 'Unit_RS') {
            $wo_unresolved = $workOrderModel->getWorkOrderUnresolved($userId, 10);
            $aset_rusak_ruangan = $asetModel->getAsetRusakByRuangan($idRuang);

            $notifications = [];
            foreach (array_slice($wo_unresolved, 0, 5) as $wo) {
                $notifications[] = [
                    'type' => 'wo_unresolved',
                    'icon' => '🔔',
                    'title' => 'WO Belum Ditangani',
                    'message' => $wo->kode_label . ' - ' . ($wo->nama_alat ?? 'Equipment'),
                    'color' => '#f97316',
                    'action_url' => BASEURL . '/aset/detail/' . $wo->id_aset
                ];
            }
            foreach (array_slice($aset_rusak_ruangan, 0, 3) as $aset) {
                $notifications[] = [
                    'type' => 'aset_rusak',
                    'icon' => '⚠️',
                    'title' => 'Aset Rusak di Ruangan',
                    'message' => $aset->kode_label . ' - ' . $aset->status_kondisi,
                    'color' => '#ef4444',
                    'action_url' => BASEURL . '/aset/detail/' . $aset->id_aset
                ];
            }
            $data['notifications'] = array_slice($notifications, 0, 10);
            $data['notification_count'] = count($notifications);
        }

        // Jika role Unit_RS, tampilkan hanya aset di ruangannya
        if ($role === 'Unit_RS' && !empty($idRuang)) {

            $data['total_aset'] = $asetModel->countAsetByRuangan($idRuang);

            $data['total_medis'] = $asetModel->countByKategoriAndRuangan('Medis', $idRuang);
            $data['total_sarpras'] = $asetModel->countByKategoriAndRuangan('Sarpras', $idRuang);
            $data['total_it'] = $asetModel->countByKategoriAndRuangan('IT', $idRuang);

            $data['baik'] = $asetModel->countByKondisiAndRuangan('Baik', $idRuang);
            $data['rusak_ringan'] = $asetModel->countByKondisiAndRuangan('Rusak_Ringan', $idRuang);
            $data['rusak_berat'] = $asetModel->countByKondisiAndRuangan('Rusak_Berat', $idRuang);
            $data['maintenance'] = $asetModel->countByKondisiAndRuangan('Maintenance', $idRuang);
            $data['gudang'] = $asetModel->countByKondisiAndRuangan('Gudang', $idRuang);

            $ruanganStats = $asetModel->getAsetPerRuanganById($idRuang);

            $data['ruangan_labels'] = array_map(function($item) {
                return $item->nama_ruang;
            }, $ruanganStats);

            $data['ruangan_totals'] = array_map(function($item) {
                return (int)$item->total_aset;
            }, $ruanganStats);

            $data['page_subheading'] = 'Ringkasan data aset pada ruangan/unit Anda.';

            // SIDEBAR DATA - UNIT PENGGUNA
            $data['sidebar_data'] = [
                'wo_buat' => $workOrderModel->getWorkOrderByReporter($userId, 5),
                'wo_unresolved' => $workOrderModel->getWorkOrderUnresolved($userId, 5),
                'aset_ruangan' => $asetModel->getAsetByRuanganUnitUser($idRuang, 10),
                'aset_rusak_ruangan' => $asetModel->getAsetRusakByRuangan($idRuang),
                'wo_recent_update' => $workOrderModel->getWorkOrderRecentUpdate($userId, 3),
                'wo_completed' => $workOrderModel->getWorkOrderCompleted($userId, 3)
            ];

        } else if ($role === 'Staf_IPSRS') {
            // Staf IPSRS lihat semua data
            $data['total_aset'] = $asetModel->countAllAset();

            $data['total_medis'] = $asetModel->countByKategori('Medis');
            $data['total_sarpras'] = $asetModel->countByKategori('Sarpras');
            $data['total_it'] = $asetModel->countByKategori('IT');

            $data['baik'] = $asetModel->countByKondisi('Baik');
            $data['rusak_ringan'] = $asetModel->countByKondisi('Rusak_Ringan');
            $data['rusak_berat'] = $asetModel->countByKondisi('Rusak_Berat');
            $data['maintenance'] = $asetModel->countByKondisi('Maintenance');
            $data['gudang'] = $asetModel->countByKondisi('Gudang');

            $ruanganStats = $asetModel->getAsetPerRuangan(8);

            $data['ruangan_labels'] = array_map(function($item) {
                return $item->nama_ruang;
            }, $ruanganStats);

            $data['ruangan_totals'] = array_map(function($item) {
                return (int)$item->total_aset;
            }, $ruanganStats);

            $data['page_subheading'] = 'Ringkasan data aset dan kondisi alat.';

            // SIDEBAR DATA - STAF IPSRS
            $data['sidebar_data'] = [
                'wo_open' => $workOrderModel->getWorkOrderOpen(5),
                'wo_priority' => $workOrderModel->getWorkOrderPriority(5),
                'wo_progress' => $workOrderModel->getWorkOrderInProgress(5),
                'kalibrasi_mendekati' => $maintenanceModel->getKalibrasiMendekati(5),
                'maintenance_hari_ini' => $maintenanceModel->getMaintenanceHariIni(),
                'aset_rusak' => $asetModel->getAsetRusak(),
                'aset_kritis' => $asetModel->getAsetKritis()
            ];

        } else if ($role === 'Staf_Logistik') {
            // Staf Logistik lihat semua data
            $data['total_aset'] = $asetModel->countAllAset();

            $data['total_medis'] = $asetModel->countByKategori('Medis');
            $data['total_sarpras'] = $asetModel->countByKategori('Sarpras');
            $data['total_it'] = $asetModel->countByKategori('IT');

            $data['baik'] = $asetModel->countByKondisi('Baik');
            $data['rusak_ringan'] = $asetModel->countByKondisi('Rusak_Ringan');
            $data['rusak_berat'] = $asetModel->countByKondisi('Rusak_Berat');
            $data['maintenance'] = $asetModel->countByKondisi('Maintenance');
            $data['gudang'] = $asetModel->countByKondisi('Gudang');

            $ruanganStats = $asetModel->getAsetPerRuangan(8);

            $data['ruangan_labels'] = array_map(function($item) {
                return $item->nama_ruang;
            }, $ruanganStats);

            $data['ruangan_totals'] = array_map(function($item) {
                return (int)$item->total_aset;
            }, $ruanganStats);

            $data['page_subheading'] = 'Ringkasan data aset dan kondisi alat.';

            // SIDEBAR DATA - STAF LOGISTIK
            $data['sidebar_data'] = [
                'aset_recent' => $asetModel->getRecentAset(5),
                'aset_gudang' => $asetModel->getAsetGudang(),
                'mutasi_terbaru' => $mutasiModel->getMutasiTerbaru(5),
                'mutasi_kel_masuk' => $mutasiModel->getMutasiKelMasuk(5),
                'aset_perlu_pengadaan' => $asetModel->getAsetPerluPengadaan(),
                'aset_tanpa_sertifikat' => $asetModel->getAsetTanpaSerifikat(),
                'aset_per_kategori' => $asetModel->countByKategori('Medis'),
                'top_ruangan' => $asetModel->getAsetPerRuangan(5)
            ];

        } else {
            // Default untuk admin & kepala
            $data['total_aset'] = $asetModel->countAllAset();

            $data['total_medis'] = $asetModel->countByKategori('Medis');
            $data['total_sarpras'] = $asetModel->countByKategori('Sarpras');
            $data['total_it'] = $asetModel->countByKategori('IT');

            $data['baik'] = $asetModel->countByKondisi('Baik');
            $data['rusak_ringan'] = $asetModel->countByKondisi('Rusak_Ringan');
            $data['rusak_berat'] = $asetModel->countByKondisi('Rusak_Berat');
            $data['maintenance'] = $asetModel->countByKondisi('Maintenance');
            $data['gudang'] = $asetModel->countByKondisi('Gudang');

            $ruanganStats = $asetModel->getAsetPerRuangan(8);

            $data['ruangan_labels'] = array_map(function($item) {
                return $item->nama_ruang;
            }, $ruanganStats);

            $data['ruangan_totals'] = array_map(function($item) {
                return (int)$item->total_aset;
            }, $ruanganStats);

            $data['page_subheading'] = 'Ringkasan data aset dan kondisi alat.';
        }

        $data['judul'] = 'Dashboard - MedTrack IPSRS';
        $data['page_heading'] = 'Dashboard';
        $data['content_view'] = 'dashboard/index';

        $this->view('templates/dashboard_layout', $data);
    }
}
