<?php

class Direktori extends Controller {

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

        $userModel = $this->model('User_model');
        $asetModel = $this->model('Aset_model');

        $data['judul'] = 'Direktori Unit & SDM - MedTrack IPSRS';
        $data['page_heading'] = 'Direktori Unit & SDM';
        $data['page_subheading'] = 'Daftar unit, ruangan, dan sumber daya manusia.';
        $data['content_view'] = 'direktori/index';
        $data['flash'] = getFlashMessage();

        // Get rooms with asset count
        $ruangan = $asetModel->getAllRuangan();
        $data['ruangan_list'] = $ruangan;

        // Get all staff
        $data['staff_list'] = $userModel->getAllUsers();

        $this->view('templates/dashboard_layout', $data);
    }
}

