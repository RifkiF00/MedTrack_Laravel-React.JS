<?php

class Auth extends Controller {
    
    public function index() {
        // Jika sudah login, paksa ke dashboard
        if (isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/dashboard');
            exit;
        }

        $data['judul'] = 'Login - MedTrack IPSRS';
        $data['error'] = '';

        $this->view('templates/header', $data);
        $this->view('auth/login', $data);
        $this->view('templates/footer');
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                die('CSRF token tidak valid.');
            }

            $username = sanitizeInput($_POST['username'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->model('User_model')->getUserByUsername($username);

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id_user'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['nama_lengkap'] = $user['nama_lengkap'];
                $_SESSION['id_ruang'] = $user['id_ruang'] ?? null;

                // Update last login
                $userModel = $this->model('User_model');
                // Bisa ditambah update last_login jika diperlukan

                header('Location: ' . BASEURL . '/dashboard');
                exit;
            } else {
                $data['judul'] = 'Login - MedTrack IPSRS';
                $data['error'] = 'Username atau Password salah!';

                $this->view('templates/header', $data);
                $this->view('auth/login', $data);
                $this->view('templates/footer');
            }
        } else {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }
    }

    public function logout() {
        $_SESSION = [];
        session_unset();
        session_destroy();
        header('Location: ' . BASEURL . '/auth');
        exit;
    }
}