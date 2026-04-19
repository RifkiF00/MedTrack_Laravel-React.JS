<?php

class Dokumen extends Controller {

    private function guard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }

        // Staf IPSRS & Staf Logistik
        if (!in_array($_SESSION['role'], ['Staf_IPSRS', 'Staf_Logistik'])) {
            http_response_code(403);
            die('Akses ditolak. Fitur ini hanya untuk Staf IPSRS dan Staf Logistik.');
        }
    }

    public function index() {
        $this->guard();

        $data['judul'] = 'Dokumen Mutu - MedTrack IPSRS';
        $data['page_heading'] = 'Dokumen Mutu';
        $data['page_subheading'] = 'Arsip dokumen kalibrasi, sertifikat, dan laporan.';
        $data['content_view'] = 'dokumen/index';
        $data['flash'] = getFlashMessage();

        // Get documents (placeholder - can integrate with model later)
        $data['dokumen_list'] = [];

        $this->view('templates/dashboard_layout', $data);
    }
}

