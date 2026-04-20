<?php
// File: app/models/User_model.php

class User_model {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Fungsi untuk mencari user berdasarkan username yang diinput
    public function getUserByUsername($username) {
        $query = "SELECT * FROM m_user WHERE username = :username AND status = 'Aktif'";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['username' => $username]);
        
        return $stmt->fetch();
    }

    // Ambil semua user aktif berdasarkan role
    public function getUsersByRole($role) {
        $query = "SELECT id_user, nama_lengkap, username, role
                  FROM m_user
                  WHERE role = :role AND status = 'Aktif'
                  ORDER BY nama_lengkap ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['role' => $role]);

        return $stmt->fetchAll();
    }

    // Ambil semua user dengan detail ruangan
    public function getAllUsers() {
        $query = "SELECT u.id_user, u.nama_lengkap, u.username, u.role, r.nama_ruang
                  FROM m_user u
                  LEFT JOIN m_ruangan r ON u.id_ruang = r.id_ruang
                  WHERE u.status = 'Aktif'
                  ORDER BY u.role ASC, u.nama_lengkap ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // Get complete profile data for a user with room information
    public function getProfileData($user_id) {
        $query = "SELECT u.id_user, u.username, u.email, u.nama_lengkap, u.role, u.nip, u.no_hp, u.status, u.id_ruang, r.nama_ruang
                  FROM m_user u
                  LEFT JOIN m_ruangan r ON u.id_ruang = r.id_ruang
                  WHERE u.id_user = :id_user";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id_user' => $user_id]);

        return $stmt->fetch();
    }

    // Upload profile photo with validation
    public function uploadProfilePhoto($user_id, $file) {
        // Validate file exists and no upload error
        if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload error'];
        }

        // Allowed extensions
        $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
        $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_ext)) {
            return ['success' => false, 'message' => 'Format file harus jpg, jpeg, png, atau webp'];
        }

        // Validate file size (5MB max)
        $max_size = 5 * 1024 * 1024; // 5MB in bytes
        if ($file['size'] > $max_size) {
            return ['success' => false, 'message' => 'Ukuran file maksimal 5MB'];
        }

        // Create upload directory if not exists
        $upload_dir = '../public/uploads/profiles/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }

        // Generate filename: profile_[user_id].[extension]
        $filename = 'profile_' . $user_id . '.' . $file_ext;
        $filepath = $upload_dir . $filename;

        // Delete old profile photo if exists
        foreach ($allowed_ext as $ext) {
            $old_file = $upload_dir . 'profile_' . $user_id . '.' . $ext;
            if (file_exists($old_file) && $ext !== $file_ext) {
                unlink($old_file);
            }
        }

        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filepath)) {
            return ['success' => true, 'message' => 'Foto profile berhasil diupload', 'filename' => $filename];
        } else {
            return ['success' => false, 'message' => 'Gagal upload file'];
        }
    }

    // Get all departments/ruangan for dropdown
    public function getAllRuangan() {
        $query = "SELECT id_ruang, nama_ruang FROM m_ruangan ORDER BY nama_ruang ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Update user profile information
    public function updateUserProfile($user_id, $data) {
        $query = "UPDATE m_user SET
                    email = :email,
                    no_hp = :no_hp,
                    nip = :nip,
                    updated_at = NOW()
                  WHERE id_user = :id_user";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id_user' => $user_id,
            'email' => $data['email'],
            'no_hp' => $data['no_hp'],
            'nip' => $data['nip'] ?? null
        ]);
    }
}