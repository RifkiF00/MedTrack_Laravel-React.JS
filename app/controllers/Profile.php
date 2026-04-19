<?php

class Profile extends Controller {

    private function guard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }
    }

    public function index() {
        $this->guard();

        $userModel = $this->model('User_model');

        $data['judul'] = 'Profil Pengguna - MedTrack IPSRS';
        $data['page_heading'] = 'Profil Pengguna';
        $data['page_subheading'] = 'Informasi akun dan pengaturan profil Anda';
        $data['content_view'] = 'profile/index';
        $data['flash'] = getFlashMessage();

        $this->view('templates/dashboard_layout', $data);
    }
}
