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

        $data['judul'] = 'Preventive Maintenance - MedTrack IPSRS';
        $data['page_heading'] = 'Preventive Maintenance';
        $data['page_subheading'] = 'Jadwal dan log pemeliharaan rutin aset.';
        $data['content_view'] = 'maintenance/index';
        $data['flash'] = getFlashMessage();

        $this->view('templates/dashboard_layout', $data);
    }
}
