<?php
// File: app/controllers/Aset.php

class Aset extends Controller {

    private $allowedRoles = ['Staf_IPSRS', 'Staf_Logistik'];

    private function guard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }

        if (!in_array($_SESSION['role'], $this->allowedRoles)) {
            http_response_code(403);
            die('Akses ditolak. Fitur ini hanya untuk Staf IPSRS dan Staf Logistik.');
        }
    }

    private function validateInput($post) {
        $errors = [];

        if (empty(trim($post['kode_label'] ?? ''))) {
            $errors[] = 'Kode label wajib diisi.';
        }

        if (empty(trim($post['nama_alat'] ?? ''))) {
            $errors[] = 'Nama alat wajib diisi.';
        }

        if (empty(trim($post['status_kondisi'] ?? ''))) {
            $errors[] = 'Status kondisi wajib dipilih.';
        }

        return $errors;
    }

    private function collectFormData() {
        return [
            'kode_label' => sanitizeInput($_POST['kode_label'] ?? ''),
            'nama_alat' => sanitizeInput($_POST['nama_alat'] ?? ''),
            'kategori_aset' => sanitizeInput($_POST['kategori_aset'] ?? 'Medis'),

            'jumlah_unit' => max(1, (int)($_POST['jumlah_unit'] ?? 1)),

            'merk' => sanitizeInput($_POST['merk'] ?? ''),
            'model' => sanitizeInput($_POST['model'] ?? ''),
            'serial_number' => sanitizeInput($_POST['serial_number'] ?? ''),
            'no_sertifikat' => sanitizeInput($_POST['no_sertifikat'] ?? ''),

            'tgl_pengadaan' => $_POST['tgl_pengadaan'] ?: null,
            'tgl_kalibrasi_terakhir' => $_POST['tgl_kalibrasi_terakhir'] ?: null,

            'harga_perolehan' => (float)($_POST['harga_perolehan'] ?? 0),

            'status_kondisi' => sanitizeInput($_POST['status_kondisi'] ?? ''),

            'id_ruang_saat_ini' => (int)($_POST['id_ruang_saat_ini'] ?? 0),

            'lokasi_fisik' => sanitizeInput($_POST['lokasi_fisik'] ?? ''),

            'keterangan' => sanitizeInput($_POST['keterangan'] ?? ''),

            'gambar_aset' => sanitizeInput($_POST['gambar_aset'] ?? ''),

            'latitude' => ($_POST['latitude'] !== '') ? (float)$_POST['latitude'] : null,
            'longitude' => ($_POST['longitude'] !== '') ? (float)$_POST['longitude'] : null
        ];
    }

    public function index() {
        $this->guard();

        $asetModel = $this->model('Aset_model');
        $maintenanceModel = $this->model('Maintenance_model');

        // 🔥 FILTER + SEARCH
        $search = $_GET['search'] ?? '';
        $kategori = $_GET['kategori'] ?? '';
        $kondisi = $_GET['kondisi'] ?? '';

        $data['aset'] = $asetModel->getFilteredAset($search, $kategori, $kondisi);

        $data['filter'] = [
            'search' => $search,
            'kategori' => $kategori,
            'kondisi' => $kondisi
        ];

        // CALIBRATION SCHEDULE DATA
        $today = date('Y-m-d');
        $data['jadwal_hari_ini_maintenance'] = $maintenanceModel->getMaintenanceByDate($today);
        $data['jadwal_minggu_maintenance'] = $maintenanceModel->getJadwalMingguIni();

        // ASSET CONDITION DATA - For sidebar
        $data['aset_rusak_ringan'] = $asetModel->getAsetByKondisi('Rusak Ringan', 5);
        $data['aset_rusak_berat'] = $asetModel->getAsetByKondisi('Rusak Berat', 5);
        $data['aset_maintenance'] = $asetModel->getAsetByKondisi('Maintenance', 5);
        $data['aset_gudang'] = $asetModel->getAsetByKondisi('Gudang', 5);

        $data['judul'] = 'Master Aset - MedTrack IPSRS';
        $data['page_heading'] = 'Master Aset';
        $data['page_subheading'] = 'Kelola data alat medis dan sarana prasarana.';
        $data['flash'] = getFlashMessage();
        $data['content_view'] = 'aset/index';

        $this->view('templates/dashboard_layout', $data);
    }

    public function create() {
        $this->guard();

        $asetModel = $this->model('Aset_model');

        $data['judul'] = 'Tambah Aset - MedTrack IPSRS';
        $data['page_heading'] = 'Tambah Aset';
        $data['page_subheading'] = 'Input data alat medis baru.';
        $data['ruangan'] = $asetModel->getAllRuangan();
        $data['errors'] = [];
        $data['old'] = [];
        $data['content_view'] = 'aset/create';

        $this->view('templates/dashboard_layout', $data);
    }

    public function store() {
        $this->guard();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/aset');
            exit;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $asetModel = $this->model('Aset_model');
        $formData = $this->collectFormData();
        $errors = $this->validateInput($_POST);

        if (!empty($errors)) {
            $data['judul'] = 'Tambah Aset - MedTrack IPSRS';
            $data['ruangan'] = $asetModel->getAllRuangan();
            $data['errors'] = $errors;
            $data['old'] = $formData;

            $data['page_heading'] = 'Tambah Aset';
            $data['page_subheading'] = 'Input data alat medis baru.';
            $data['content_view'] = 'aset/create';

            $this->view('templates/dashboard_layout', $data);
            return;
        }

        $id = $asetModel->createAset($formData);

        if ($id) {
            // 🔥 generate QR
            $qrFile = $this->generateQRCode($id);

            // 🔥 simpan ke database
            $asetModel->updateQRCode($id, $qrFile);

            redirectWithMessage(BASEURL . '/aset/detail/' . $id, 'Data aset + QR berhasil ditambahkan.', 'success');
        } else {
            redirectWithMessage(BASEURL . '/aset', 'Gagal menambahkan data aset.', 'danger');
        }
    }

    // 🔥 GENERATE QR CODE
    private function generateQRCode($id) {
        require_once '../public/libs/phpqrcode/qrlib.php';

        $dir = '../public/uploads/qr/';
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $fileName = 'aset_' . $id . '.png';
        $filePath = $dir . $fileName;

        $url = BASEURL . '/aset/detail/' . $id;

        QRcode::png($url, $filePath, QR_ECLEVEL_L, 5);

        return $fileName;
    }

    public function edit($id = null) {
        $this->guard();

        if (!$id) {
            header('Location: ' . BASEURL . '/aset');
            exit;
        }

        $asetModel = $this->model('Aset_model');
        $aset = $asetModel->getAsetById($id);

        if (!$aset) {
            redirectWithMessage(BASEURL . '/aset', 'Data aset tidak ditemukan.', 'danger');
        }

        $data['judul'] = 'Edit Aset - MedTrack IPSRS';
        $data['page_heading'] = 'Edit Aset';
        $data['page_subheading'] = 'Perbarui data alat medis.';
        $data['aset'] = $aset;
        $data['ruangan'] = $asetModel->getAllRuangan();
        $data['errors'] = [];
        $data['content_view'] = 'aset/edit';

        $this->view('templates/dashboard_layout', $data);
    }

    public function update($id = null) {
        $this->guard();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            header('Location: ' . BASEURL . '/aset');
            exit;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $asetModel = $this->model('Aset_model');
        $aset = $asetModel->getAsetById($id);

        if (!$aset) {
            redirectWithMessage(BASEURL . '/aset', 'Data aset tidak ditemukan.', 'danger');
        }

        $formData = $this->collectFormData();
        $errors = $this->validateInput($_POST);

        if (!empty($errors)) {
            $data['judul'] = 'Edit Aset - MedTrack IPSRS';
            $data['aset'] = array_merge($aset, $formData);
            $data['ruangan'] = $asetModel->getAllRuangan();
            $data['errors'] = $errors;

            $data['page_heading'] = 'Edit Aset';
            $data['page_subheading'] = 'Perbarui data alat medis.';
            $data['content_view'] = 'aset/edit';

            $this->view('templates/dashboard_layout', $data);
            return;
        }

        if ($asetModel->updateAset($id, $formData)) {
            redirectWithMessage(BASEURL . '/aset', 'Data aset berhasil diperbarui.', 'success');
        } else {
            redirectWithMessage(BASEURL . '/aset', 'Gagal memperbarui data aset.', 'danger');
        }
    }

    public function delete($id = null) {
        $this->guard();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !$id) {
            header('Location: ' . BASEURL . '/aset');
            exit;
        }

        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        $asetModel = $this->model('Aset_model');

        if ($asetModel->deleteAset($id)) {
            redirectWithMessage(BASEURL . '/aset', 'Data aset berhasil dihapus.', 'success');
        } else {
            redirectWithMessage(BASEURL . '/aset', 'Gagal menghapus data aset.', 'danger');
        }
    }

    // 🔥 DETAIL ASET (UNTUK QR SCAN)
    public function detail($id = null) {

        if (!$id) {
            echo "ID tidak valid";
            return;
        }

        $asetModel = $this->model('Aset_model');
        $aset = $asetModel->getAsetById($id);

        if (!$aset) {
            echo "Aset tidak ditemukan";
            return;
        }

        $data['judul'] = 'Detail Aset';
        $data['aset'] = $aset;
        $data['role'] = $_SESSION['role'] ?? 'Guest';
        $data['content_view'] = 'aset/detail';

        $this->view('templates/dashboard_layout', $data);
    }

    public function scan() {
        // Guard: check login saja (tidak perlu role check)
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }

        $data['judul'] = 'Scan QR Aset - MedTrack IPSRS';
        $data['page_heading'] = 'Scan QR Aset';
        $data['page_subheading'] = 'Arahkan kamera ke QR code aset.';
        $data['content_view'] = 'aset/scan';

        $this->view('templates/dashboard_layout', $data);
    }

    // 🔥 LIST ASET UNTUK UNIT (Detail Aset)
    public function listunit() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }

        // Hanya Unit_RS yang bisa akses
        if ($_SESSION['role'] !== 'Unit_RS') {
            http_response_code(403);
            die('Akses ditolak. Fitur ini hanya untuk Unit.');
        }

        $asetModel = $this->model('Aset_model');
        $idRuang = $_SESSION['id_ruang'] ?? null;

        if (!$idRuang) {
            redirectWithMessage(BASEURL . '/dashboard', 'Ruangan tidak ditemukan di profil Anda.', 'danger');
            return;
        }

        // Get aset by ruangan
        $data['aset'] = $asetModel->getAsetByRuangan($idRuang);

        // Get ruangan info
        $ruangan = $asetModel->getAllRuangan();
        $namaRuang = '';
        foreach ($ruangan as $r) {
            if ($r->id_ruang == $idRuang) {
                $namaRuang = $r->nama_ruang;
                break;
            }
        }

        $data['judul'] = 'Detail Aset - MedTrack IPSRS';
        $data['page_heading'] = 'Detail Aset';
        $data['page_subheading'] = 'Daftar alat medis & sarana prasarana di ruangan ' . $namaRuang;
        $data['nama_ruang'] = $namaRuang;
        $data['content_view'] = 'aset/list_unit';

        $this->view('templates/dashboard_layout', $data);
    }

    // 🔥 GENERATE QR MASSAL UNTUK SEMUA ASET
    public function generateQRAll() {
        $this->guard();

        $asetModel = $this->model('Aset_model');
        $allAset = $asetModel->getAllAsetBasic();

        if (empty($allAset)) {
            redirectWithMessage(BASEURL . '/aset', 'Tidak ada data aset untuk dibuatkan QR.', 'danger');
            return;
        }

        $totalGenerated = 0;

        foreach ($allAset as $aset) {
            $id = (int)($aset->id_aset ?? 0);

            if ($id <= 0) {
                continue;
            }

            $qrFile = $this->generateQRCode($id);

            if ($qrFile) {
                $asetModel->updateQRCode($id, $qrFile);
                $totalGenerated++;
            }
        }

        redirectWithMessage(
            BASEURL . '/aset',
            'Berhasil generate QR untuk ' . $totalGenerated . ' aset.',
            'success'
        );
    }
}