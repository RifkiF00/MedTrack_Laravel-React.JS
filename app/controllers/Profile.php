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

        // Load User model
        $userModel = $this->model('User_model');

        // Handle file upload
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
            $upload_result = $userModel->uploadProfilePhoto($_SESSION['user_id'], $_FILES['profile_photo']);
            if ($upload_result['success']) {
                $_SESSION['flash'] = ['type' => 'success', 'message' => $upload_result['message']];
            } else {
                $_SESSION['flash'] = ['type' => 'error', 'message' => $upload_result['message']];
            }
            // Redirect to refresh page
            header('Location: ' . BASEURL . '/profile');
            exit;
        }

        // Get user profile data from database
        $user_data = $userModel->getProfileData($_SESSION['user_id']);

        $data['judul'] = 'Profil Pengguna - MedTrack IPSRS';
        $data['page_heading'] = 'Profil Pengguna';
        $data['page_subheading'] = 'Informasi akun dan pengaturan profil Anda';
        $data['content_view'] = 'profile/index';
        $data['flash'] = getFlashMessage();
        $data['user'] = $user_data;

        $this->view('templates/dashboard_layout', $data);
    }
}
