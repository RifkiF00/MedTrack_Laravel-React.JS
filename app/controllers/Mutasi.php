<?php

class Mutasi extends Controller {

    private function guard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }

        // Allow: Staf IPSRS, Staf Logistik, Unit RS
        if (!in_array($_SESSION['role'], ['Staf_IPSRS', 'Staf_Logistik', 'Unit_RS'])) {
            http_response_code(403);
            die('Akses ditolak. Fitur ini hanya untuk Staf IPSRS, Staf Logistik, dan Unit RS.');
        }
    }

    private function guardIPSRS() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }

        // Hanya IPSRS yang bisa approve/reject/complete
        if ($_SESSION['role'] !== 'Staf_IPSRS') {
            http_response_code(403);
            die('Akses ditolak. Fitur ini hanya untuk Staf IPSRS.');
        }
    }

    public function index() {
        $this->guard();

        $mutasiModel = $this->model('Mutasi_model');
        $asetModel = $this->model('Aset_model');
        $role = $_SESSION['role'] ?? '';
        $userId = $_SESSION['user_id'] ?? null;

        $data['judul'] = 'Mutasi Ruangan - MedTrack IPSRS';
        $data['page_heading'] = 'Mutasi Ruangan';
        $data['content_view'] = 'mutasi/index';
        $data['flash'] = getFlashMessage();

        // Filter berdasarkan role
        if ($role === 'Unit_RS') {
            // Unit_RS hanya lihat mutasi yang mereka buat
            $data['mutasi_list'] = $mutasiModel->getMutasiByUser($userId);
            $data['page_subheading'] = 'Permintaan mutasi aset di ruangan Anda';
        } else {
            // IPSRS & Logistik lihat semua mutasi
            $data['mutasi_list'] = $mutasiModel->getAllMutasi();
            $data['page_subheading'] = 'Pencatatan pergerakan aset antar ruangan';
        }

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

            $ruang_asal = (int)($_POST['ruang_asal'] ?? 0);
            $ruang_tujuan = (int)($_POST['ruang_tujuan'] ?? 0);
            $id_aset = (int)($_POST['id_aset'] ?? 0);

            // Validasi ruang_asal tidak boleh kosong
            if (empty($ruang_asal)) {
                setFlashMessage('Ruangan asal tidak valid. Pilih aset yang sudah memiliki ruangan.', 'error');
                header('Location: ' . BASEURL . '/mutasi/create');
                exit;
            }

            // Validasi ruang_tujuan
            if (empty($ruang_tujuan)) {
                setFlashMessage('Ruangan tujuan harus dipilih', 'error');
                header('Location: ' . BASEURL . '/mutasi/create');
                exit;
            }

            // UNTUK UNIT_RS: Validasi asset harus di ruangan mereka
            if ($_SESSION['role'] === 'Unit_RS') {
                $idRuang = $_SESSION['id_ruang'] ?? null;
                if (empty($idRuang) || $ruang_asal != $idRuang) {
                    setFlashMessage('Anda hanya bisa membuat mutasi untuk aset di ruangan Anda', 'error');
                    header('Location: ' . BASEURL . '/mutasi/create');
                    exit;
                }
            }

            $mutasiModel = $this->model('Mutasi_model');

            $mutasiData = [
                'id_aset' => $id_aset,
                'ruang_asal' => $ruang_asal,
                'ruang_tujuan' => $ruang_tujuan,
                'id_user_pencatat' => $_SESSION['user_id'],
                'alasan_mutasi' => htmlspecialchars($_POST['alasan_mutasi'] ?? ''),
                'status_mutasi' => 'Menunggu_Verifikasi',
                'catatan' => htmlspecialchars($_POST['catatan'] ?? '')
            ];

            if ($mutasiModel->createMutasi($mutasiData)) {
                setFlashMessage('Mutasi ruangan berhasil dicatat dan menunggu verifikasi', 'success');
            } else {
                setFlashMessage('Gagal mencatat mutasi ruangan', 'error');
            }

            header('Location: ' . BASEURL . '/mutasi');
            exit;
        }

        // Tampilkan form
        $asetModel = $this->model('Aset_model');
        $role = $_SESSION['role'];
        $idRuang = $_SESSION['id_ruang'] ?? null;

        $data['judul'] = 'Tambah Mutasi - MedTrack IPSRS';
        $data['page_heading'] = 'Catat Mutasi Ruangan';
        $data['page_subheading'] = 'Pencatatan pergerakan aset ke ruangan lain';
        $data['content_view'] = 'mutasi/create';
        $data['flash'] = getFlashMessage();

        // Filter asset list berdasarkan role
        if ($role === 'Unit_RS' && !empty($idRuang)) {
            // Unit_RS hanya lihat aset di ruangan mereka
            $asetList = $asetModel->getAsetByRuangan($idRuang);
        } else {
            // IPSRS & Logistik lihat semua aset
            $asetList = $asetModel->getAllAset();
        }

        // Filter hanya aset yang sudah memiliki ruangan
        $data['aset_list'] = array_filter($asetList, function($aset) {
            return !empty($aset->id_ruang_saat_ini);
        });

        $data['ruangan_list'] = $asetModel->getAllRuangan();

        $this->view('templates/dashboard_layout', $data);
    }

    public function approve($id) {
        $this->guardIPSRS();

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
        $this->guardIPSRS();

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
        $this->guardIPSRS();

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
        $this->guardIPSRS();

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
