<?php
// File: app/models/WorkOrder_model.php

class WorkOrder_model {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // 🔹 GET ALL TROUBLESHOOT / WORK ORDER
    public function getAllWorkOrders() {
        $query = "SELECT
                    t.id_ticket,
                    t.id_aset,
                    t.id_user_pelapor,
                    t.id_teknisi_penanggungjawab,
                    t.tgl_lapor,
                    t.tingkat_urgensi,
                    t.deskripsi_kerusakan,
                    t.foto_kerusakan,
                    t.status_ticket,
                    a.kode_label,
                    a.nama_alat,
                    r.nama_ruang,
                    u.nama_lengkap AS nama_pelapor,
                    tech.nama_lengkap AS nama_teknisi
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  LEFT JOIN m_ruangan r ON a.id_ruang_saat_ini = r.id_ruang
                  LEFT JOIN m_user u ON t.id_user_pelapor = u.id_user
                  LEFT JOIN m_user tech ON t.id_teknisi_penanggungjawab = tech.id_user
                  ORDER BY t.tgl_lapor DESC, t.id_ticket DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // 🔹 GET WORK ORDER BY ID
    public function getWorkOrderById($id_ticket) {
        $query = "SELECT
                    t.id_ticket,
                    t.id_aset,
                    t.id_user_pelapor,
                    t.id_teknisi_penanggungjawab,
                    t.tgl_lapor,
                    t.tingkat_urgensi,
                    t.deskripsi_kerusakan,
                    t.foto_kerusakan,
                    t.status_ticket,
                    a.kode_label,
                    a.nama_alat,
                    r.nama_ruang,
                    u.nama_lengkap AS nama_pelapor,
                    tech.nama_lengkap AS nama_teknisi
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  LEFT JOIN m_ruangan r ON a.id_ruang_saat_ini = r.id_ruang
                  LEFT JOIN m_user u ON t.id_user_pelapor = u.id_user
                  LEFT JOIN m_user tech ON t.id_teknisi_penanggungjawab = tech.id_user
                  WHERE t.id_ticket = :id_ticket
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id_ticket' => $id_ticket]);

        return $stmt->fetch();
    }

    // 🔹 CREATE WORK ORDER
    public function createWorkOrder($data) {
        $query = "INSERT INTO t_troubleshoot (
                    id_aset,
                    id_user_pelapor,
                    id_teknisi_penanggungjawab,
                    tgl_lapor,
                    tingkat_urgensi,
                    deskripsi_kerusakan,
                    foto_kerusakan,
                    status_ticket
                  ) VALUES (
                    :id_aset,
                    :id_user_pelapor,
                    :id_teknisi_penanggungjawab,
                    NOW(),
                    :tingkat_urgensi,
                    :deskripsi_kerusakan,
                    :foto_kerusakan,
                    :status_ticket
                  )";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            'id_aset' => $data['id_aset'],
            'id_user_pelapor' => $data['id_user_pelapor'],
            'id_teknisi_penanggungjawab' => $data['id_teknisi_penanggungjawab'] ?? null,
            'tingkat_urgensi' => $data['tingkat_urgensi'],
            'deskripsi_kerusakan' => $data['deskripsi_kerusakan'],
            'foto_kerusakan' => $data['foto_kerusakan'] ?? null,
            'status_ticket' => $data['status_ticket']
        ]);
    }

    // 🔹 UPDATE STATUS TICKET
    public function updateStatusTicket($id_ticket, $status_ticket) {
        $query = "UPDATE t_troubleshoot
                  SET status_ticket = :status_ticket
                  WHERE id_ticket = :id_ticket";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            'id_ticket' => $id_ticket,
            'status_ticket' => $status_ticket
        ]);
    }

    // 🔹 ASSIGN TEKNISI
    public function assignTeknisi($id_ticket, $id_teknisi) {
        $query = "UPDATE t_troubleshoot
                  SET id_teknisi_penanggungjawab = :id_teknisi
                  WHERE id_ticket = :id_ticket";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            'id_ticket' => $id_ticket,
            'id_teknisi' => $id_teknisi ?: null
        ]);
    }

    // 🔹 GET WORK ORDER BY PELAPOR
    public function getWorkOrdersByPelapor($id_user_pelapor) {
        $query = "SELECT
                    t.id_ticket,
                    t.id_aset,
                    t.id_user_pelapor,
                    t.id_teknisi_penanggungjawab,
                    t.tgl_lapor,
                    t.tingkat_urgensi,
                    t.deskripsi_kerusakan,
                    t.foto_kerusakan,
                    t.status_ticket,
                    a.kode_label,
                    a.nama_alat,
                    r.nama_ruang,
                    tech.nama_lengkap AS nama_teknisi
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  LEFT JOIN m_ruangan r ON a.id_ruang_saat_ini = r.id_ruang
                  LEFT JOIN m_user tech ON t.id_teknisi_penanggungjawab = tech.id_user
                  WHERE t.id_user_pelapor = :id_user_pelapor
                  ORDER BY t.tgl_lapor DESC, t.id_ticket DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id_user_pelapor' => $id_user_pelapor]);

        return $stmt->fetchAll();
    }

    // 🔹 TAMBAH LOG STATUS
    public function addStatusLog($data) {
        $query = "INSERT INTO t_troubleshoot_log (
                    id_ticket,
                    status_lama,
                    status_baru,
                    catatan,
                    diubah_oleh
                  ) VALUES (
                    :id_ticket,
                    :status_lama,
                    :status_baru,
                    :catatan,
                    :diubah_oleh
                  )";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            'id_ticket' => $data['id_ticket'],
            'status_lama' => $data['status_lama'],
            'status_baru' => $data['status_baru'],
            'catatan' => $data['catatan'] ?? null,
            'diubah_oleh' => $data['diubah_oleh']
        ]);
    }

    // 🔹 AMBIL LOG STATUS
    public function getLogsByTicket($id_ticket) {
        $query = "SELECT
                    l.*,
                    u.nama_lengkap
                  FROM t_troubleshoot_log l
                  LEFT JOIN m_user u ON l.diubah_oleh = u.id_user
                  WHERE l.id_ticket = :id_ticket
                  ORDER BY l.created_at DESC, l.id_log DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id_ticket' => $id_ticket]);

        return $stmt->fetchAll();
    }
}