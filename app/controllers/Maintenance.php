<?php

class Maintenance extends Controller {

    private function guard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }

        // Hanya Staf IPSRS
        if ($_SESSION['role'] !== 'Staf_IPSRS') {
            http_response_code(403);
            die('Akses ditolak. Fitur ini hanya untuk Staf IPSRS.');
        }
    }

    public function index() {
        $this->guard();

        $maintenanceModel = $this->model('Maintenance_model');

        $data['judul'] = 'Preventive Maintenance - MedTrack IPSRS';
        $data['page_heading'] = 'Preventive Maintenance';
        $data['page_subheading'] = 'Jadwal dan log pemeliharaan rutin aset untuk menjaga kondisi optimal';
        $data['content_view'] = 'maintenance/index';
        $data['flash'] = getFlashMessage();

        // Ambil data untuk ditampilkan
        $data['jadwal_bulan'] = $maintenanceModel->getJadwalBulanIni();
        $data['pending_hari_ini'] = $maintenanceModel->getJadwalPendingHariIni();
        $data['statistik'] = $maintenanceModel->getStatistik();
        $data['recent_logs'] = $maintenanceModel->getLogPemeliharaan(null, date('m'), date('Y'));

        $this->view('templates/dashboard_layout', $data);
    }

    public function log() {
        $this->guard();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validasi CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                setFlashMessage('CSRF token tidak valid', 'error');
                header('Location: ' . BASEURL . '/maintenance');
                exit;
            }

            $maintenanceModel = $this->model('Maintenance_model');

            $logData = [
                'id_pemeliharaan' => htmlspecialchars($_POST['id_pemeliharaan'] ?? ''),
                'id_user_pelaksana' => $_SESSION['user_id'],
                'tgl_rencana' => htmlspecialchars($_POST['tgl_rencana'] ?? date('Y-m-d')),
                'status_pelaksanaan' => htmlspecialchars($_POST['status_pelaksanaan'] ?? 'Terselesaikan'),
                'hasil_pengecekan' => htmlspecialchars($_POST['hasil_pengecekan'] ?? ''),
                'kondisi_laporan' => htmlspecialchars($_POST['kondisi_laporan'] ?? 'Normal'),
                'catatan_khusus' => htmlspecialchars($_POST['catatan_khusus'] ?? '')
            ];

            if ($maintenanceModel->createLog($logData)) {
                setFlashMessage('Log pemeliharaan berhasil ditambahkan', 'success');
            } else {
                setFlashMessage('Gagal menambahkan log pemeliharaan', 'error');
            }

            header('Location: ' . BASEURL . '/maintenance');
            exit;
        }

        // Tampilkan form jika GET
        $maintenanceModel = $this->model('Maintenance_model');

        $data['judul'] = 'Input Log Maintenance - MedTrack IPSRS';
        $data['page_heading'] = 'Input Log Maintenance';
        $data['page_subheading'] = 'Catat pelaksanaan pemeliharaan rutin';
        $data['content_view'] = 'maintenance/log';
        $data['flash'] = getFlashMessage();

        $data['pemeliharaan_items'] = $maintenanceModel->getAllPemeliharaan();

        $this->view('templates/dashboard_layout', $data);
    }

    public function history() {
        $this->guard();

        $maintenanceModel = $this->model('Maintenance_model');

        $data['judul'] = 'Riwayat Maintenance - MedTrack IPSRS';
        $data['page_heading'] = 'Riwayat Pemeliharaan';
        $data['page_subheading'] = 'Histori pelaksanaan maintenance rutin';
        $data['content_view'] = 'maintenance/history';
        $data['flash'] = getFlashMessage();

        $data['logs'] = $maintenanceModel->getLogPemeliharaan(null, date('m'), date('Y'));
        $data['items'] = $maintenanceModel->getAllPemeliharaan();

        $this->view('templates/dashboard_layout', $data);
    }

    public function delete($id) {
        $this->guard();

        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            setFlashMessage('CSRF token tidak valid', 'error');
            header('Location: ' . BASEURL . '/maintenance');
            exit;
        }

        $maintenanceModel = $this->model('Maintenance_model');

        if ($maintenanceModel->deletePemeliharaan($id)) {
            setFlashMessage('Jadwal pemeliharaan berhasil dihapus', 'success');
        } else {
            setFlashMessage('Gagal menghapus jadwal pemeliharaan', 'error');
        }

        header('Location: ' . BASEURL . '/maintenance');
        exit;
    }

    public function reschedule() {
        $this->guard();

        // Validasi CSRF token
        if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
            setFlashMessage('CSRF token tidak valid', 'error');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }

        $id_pemeliharaan = htmlspecialchars($_POST['id_pemeliharaan'] ?? '');
        $tgl_rencana = htmlspecialchars($_POST['tgl_rencana'] ?? '');

        if (empty($id_pemeliharaan) || empty($tgl_rencana)) {
            setFlashMessage('Data tidak lengkap', 'error');
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }

        $maintenanceModel = $this->model('Maintenance_model');

        $logData = [
            'id_pemeliharaan' => $id_pemeliharaan,
            'id_user_pelaksana' => $_SESSION['user_id'],
            'tgl_rencana' => $tgl_rencana,
            'status_pelaksanaan' => 'Terjadwal',
            'hasil_pengecekan' => '',
            'kondisi_laporan' => 'Normal',
            'catatan_khusus' => 'Dijadwalkan ulang'
        ];

        if ($maintenanceModel->createLog($logData)) {
            setFlashMessage('Jadwal maintenance berhasil dijadwalkan ulang ke ' . date('d/m/Y', strtotime($tgl_rencana)), 'success');
        } else {
            setFlashMessage('Gagal menjadwalkan ulang maintenance', 'error');
        }

        header('Location: ' . BASEURL . '/dashboard');
        exit;
    }
}
