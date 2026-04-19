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
}