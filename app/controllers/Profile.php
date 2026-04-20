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

    public function edit() {
        $this->guard();

        // Load User model
        $userModel = $this->model('User_model');

        // Get user profile data
        $user_data = $userModel->getProfileData($_SESSION['user_id']);

        $data['judul'] = 'Edit Profil - MedTrack IPSRS';
        $data['page_heading'] = 'Edit Profil Pengguna';
        $data['page_subheading'] = 'Perbarui informasi akun Anda';
        $data['content_view'] = 'profile/index';
        $data['flash'] = getFlashMessage();
        $data['user'] = $user_data;
        $data['edit_mode'] = true;
        $data['ruangan'] = $userModel->getAllRuangan();
        $data['errors'] = [];

        $this->view('templates/dashboard_layout', $data);
    }

    public function update() {
        $this->guard();

        // Verify CSRF token
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
            die('CSRF token tidak valid.');
        }

        // Load User model
        $userModel = $this->model('User_model');

        // Collect form data
        $formData = [
            'email' => sanitizeInput($_POST['email'] ?? ''),
            'no_hp' => sanitizeInput($_POST['no_hp'] ?? ''),
            'nip' => sanitizeInput($_POST['nip'] ?? ''),
            'id_ruang' => !empty($_POST['id_ruang']) ? (int)$_POST['id_ruang'] : null
        ];

        // Validate input
        $errors = $this->validateProfileInput($formData);

        // If errors exist, re-display form with errors
        if (!empty($errors)) {
            $user_data = $userModel->getProfileData($_SESSION['user_id']);
            $data['judul'] = 'Edit Profil - MedTrack IPSRS';
            $data['page_heading'] = 'Edit Profil Pengguna';
            $data['page_subheading'] = 'Perbarui informasi akun Anda';
            $data['content_view'] = 'profile/index';
            $data['flash'] = getFlashMessage();
            $data['user'] = $user_data;
            $data['edit_mode'] = true;
            $data['ruangan'] = $userModel->getAllRuangan();
            $data['errors'] = $errors;
            $data['old'] = $formData;

            $this->view('templates/dashboard_layout', $data);
            return;
        }

        // Update user profile in database
        if ($userModel->updateUserProfile($_SESSION['user_id'], $formData)) {
            // Update SESSION with new values
            $_SESSION['email'] = $formData['email'];
            $_SESSION['no_hp'] = $formData['no_hp'];
            $_SESSION['nip'] = $formData['nip'];

            // Redirect with success message
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Profil berhasil diperbarui'];
            header('Location: ' . BASEURL . '/profile');
            exit;
        } else {
            // Database update failed
            $_SESSION['flash'] = ['type' => 'error', 'message' => 'Gagal memperbarui profil'];
            header('Location: ' . BASEURL . '/profile/edit');
            exit;
        }
    }

    private function validateProfileInput($data) {
        $errors = [];

        // Validate email
        if (empty(trim($data['email']))) {
            $errors[] = 'Email wajib diisi.';
        } elseif (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Format email tidak valid.';
        }

        // Validate phone
        if (empty(trim($data['no_hp']))) {
            $errors[] = 'Nomor telepon wajib diisi.';
        } elseif (!preg_match('/^\d{10,12}$/', str_replace([' ', '-', '+', '62'], '', $data['no_hp']))) {
            $errors[] = 'Nomor telepon harus 10-12 digit.';
        }

        // Validate NIP (optional but if provided must be numeric)
        if (!empty($data['nip']) && !is_numeric($data['nip'])) {
            $errors[] = 'NIP harus berupa angka.';
        }

        return $errors;
    }
}
