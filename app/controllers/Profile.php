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

        // TEST: Output tanpa layout
        ?>
<!DOCTYPE html>
<html>
<head>
    <title>Profile - MedTrack</title>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial; max-width: 600px; margin: 50px auto; padding: 20px;">
    <h1>Profil Pengguna</h1>
    <p><strong>Nama:</strong> <?= escape($_SESSION['nama_lengkap'] ?? 'User'); ?></p>
    <p><strong>Username:</strong> <?= escape($_SESSION['username'] ?? '-'); ?></p>
    <p><strong>Role:</strong> <?= escape($_SESSION['role'] ?? '-'); ?></p>
    <hr>
    <a href="<?= BASEURL; ?>/dashboard" style="padding: 10px 20px; background: #3d6aff; color: white; text-decoration: none; border-radius: 5px; display: inline-block;">
        Kembali ke Dashboard
    </a>
    <a href="<?= BASEURL; ?>/auth/logout" style="padding: 10px 20px; background: #ef4444; color: white; text-decoration: none; border-radius: 5px; display: inline-block; margin-left: 10px;">
        Logout
    </a>
</body>
</html>
        <?php
    }
}
