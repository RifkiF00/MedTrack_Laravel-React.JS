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

        $data['judul'] = 'Direktori Unit & SDM - MedTrack IPSRS';
        $data['page_heading'] = 'Direktori Unit & SDM';
        $data['page_subheading'] = 'Daftar unit, ruangan, dan sumber daya manusia.';
        $data['content_view'] = 'direktori/index';
        $data['flash'] = getFlashMessage();

        $this->view('templates/dashboard_layout', $data);
    }
}
