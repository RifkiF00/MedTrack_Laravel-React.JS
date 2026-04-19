<?php

class Mutasi extends Controller {

    private function guard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }

        // Hanya Staf IPSRS & Staf Logistik
        if (!in_array($_SESSION['role'], ['Staf_IPSRS', 'Staf_Logistik'])) {
            http_response_code(403);
            die('Akses ditolak. Fitur ini hanya untuk Staf IPSRS dan Staf Logistik.');
        }
    }

    public function index() {
        $this->guard();

        $mutasiModel = $this->model('Mutasi_model');
        $asetModel = $this->model('Aset_model');

        $data['judul'] = 'Mutasi Ruangan - MedTrack IPSRS';
        $data['page_heading'] = 'Mutasi Ruangan';
        $data['page_subheading'] = 'Pencatatan pergerakan aset antar ruangan';
        $data['content_view'] = 'mutasi/index';
        $data['flash'] = getFlashMessage();

        // Ambil data mutasi
        $data['mutasi_list'] = $mutasiModel->getAllMutasi();
        $data['statistik'] = $mutasiModel->getStatistik();

        $this->view('templates/dashboard_layout', $data);
    }

    public function create() {
        $this->guard();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validasi CSRF
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                setFlashMessage('CSRF token tidak valid', 'error');
                header('Location: ' . BASEURL . '/mutasi');
                exit;
            }

            $mutasiModel = $this->model('Mutasi_model');

            $mutasiData = [
                'id_aset' => (int)($_POST['id_aset'] ?? 0),
                'ruang_asal' => (int)($_POST['ruang_asal'] ?? 0),
                'ruang_tujuan' => (int)($_POST['ruang_tujuan'] ?? 0),
                'id_user_pencatat' => $_SESSION['user_id'],
                'alasan_mutasi' => htmlspecialchars($_POST['alasan_mutasi'] ?? ''),
                'status_mutasi' => 'Menunggu_Verifikasi',
                'catatan' => htmlspecialchars($_POST['catatan'] ?? '')
            ];

            if ($mutasiModel->createMutasi($mutasiData)) {
                setFlashMessage('Mutasi ruangan berhasil dicatat', 'success');
            } else {
                setFlashMessage('Gagal mencatat mutasi ruangan', 'error');
            }

            header('Location: ' . BASEURL . '/mutasi');
            exit;
        }

        // Tampilkan form
        $asetModel = $this->model('Aset_model');

        $data['judul'] = 'Tambah Mutasi - MedTrack IPSRS';
        $data['page_heading'] = 'Catat Mutasi Ruangan';
        $data['page_subheading'] = 'Pencatatan pergerakan aset ke ruangan lain';
        $data['content_view'] = 'mutasi/create';
        $data['flash'] = getFlashMessage();

        $data['aset_list'] = $asetModel->getAllAset();
        $data['ruangan_list'] = $asetModel->getAllRuangan();

        $this->view('templates/dashboard_layout', $data);
    }

    public function approve($id) {
        $this->guard();

        $mutasiModel = $this->model('Mutasi_model');

        if ($mutasiModel->updateStatus($id, 'Disetujui')) {
            setFlashMessage('Mutasi disetujui', 'success');
        } else {
            setFlashMessage('Gagal menyetujui mutasi', 'error');
        }

        header('Location: ' . BASEURL . '/mutasi');
        exit;
    }

    public function reject($id) {
        $this->guard();

        $mutasiModel = $this->model('Mutasi_model');

        if ($mutasiModel->updateStatus($id, 'Ditolak')) {
            setFlashMessage('Mutasi ditolak', 'success');
        } else {
            setFlashMessage('Gagal menolak mutasi', 'error');
        }

        header('Location: ' . BASEURL . '/mutasi');
        exit;
    }

    public function complete($id) {
        $this->guard();

        $mutasiModel = $this->model('Mutasi_model');

        if ($mutasiModel->selesaikanMutasi($id)) {
            setFlashMessage('Mutasi selesai - lokasi aset telah diperbarui', 'success');
        } else {
            setFlashMessage('Gagal menyelesaikan mutasi', 'error');
        }

        header('Location: ' . BASEURL . '/mutasi');
        exit;
    }

    public function delete($id) {
        $this->guard();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                setFlashMessage('CSRF token tidak valid', 'error');
                header('Location: ' . BASEURL . '/mutasi');
                exit;
            }

            $mutasiModel = $this->model('Mutasi_model');

            if ($mutasiModel->deleteMutasi($id)) {
                setFlashMessage('Mutasi dihapus', 'success');
            } else {
                setFlashMessage('Gagal menghapus mutasi', 'error');
            }
        }

        header('Location: ' . BASEURL . '/mutasi');
        exit;
    }
}
