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

    public function uploadPhoto() {
        $this->guard();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . BASEURL . '/profile');
            exit;
        }

        if (!isset($_FILES['profile_photo']) || $_FILES['profile_photo']['error'] === UPLOAD_ERR_NO_FILE) {
            redirectWithMessage(BASEURL . '/profile', 'Pilih file gambar terlebih dahulu.', 'danger');
        }

        if ($_FILES['profile_photo']['error'] !== UPLOAD_ERR_OK) {
            redirectWithMessage(BASEURL . '/profile', 'Terjadi kesalahan saat upload. Coba lagi.', 'danger');
        }

        $allowedExt = ['jpg', 'jpeg', 'png'];
        $maxSize = 5 * 1024 * 1024; // 5MB

        $fileName = $_FILES['profile_photo']['name'];
        $tmpName = $_FILES['profile_photo']['tmp_name'];
        $fileSize = $_FILES['profile_photo']['size'];
        $ext = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowedExt)) {
            redirectWithMessage(BASEURL . '/profile', 'Format file harus JPG, JPEG, atau PNG.', 'danger');
        }

        if ($fileSize > $maxSize) {
            redirectWithMessage(BASEURL . '/profile', 'Ukuran file terlalu besar (maks 5MB).', 'danger');
        }

        $uploadDir = '../public/uploads/profiles/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $newName = $_SESSION['id_user'] . '.png';
        $targetPath = $uploadDir . $newName;

        if (!move_uploaded_file($tmpName, $targetPath)) {
            redirectWithMessage(BASEURL . '/profile', 'Gagal menyimpan file gambar.', 'danger');
        }

        redirectWithMessage(BASEURL . '/profile', 'Foto profil berhasil diupload! 📸', 'success');
    }
}
