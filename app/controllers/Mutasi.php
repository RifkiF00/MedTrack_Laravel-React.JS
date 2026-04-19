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

        $data['judul'] = 'Mutasi Ruangan - MedTrack IPSRS';
        $data['page_heading'] = 'Mutasi Ruangan';
        $data['page_subheading'] = 'Pencatatan pergerakan aset antar ruangan.';
        $data['content_view'] = 'mutasi/index';
        $data['flash'] = getFlashMessage();

        $this->view('templates/dashboard_layout', $data);
    }
}
