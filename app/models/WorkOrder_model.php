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

    // 📍 SIDEBAR METHODS - STAF IPSRS & UNIT PENGGUNA

    // Get open work orders (Staf IPSRS)
    public function getWorkOrderOpen($limit = 5) {
        $query = "SELECT t.id_ticket, t.id_aset, a.kode_label, a.nama_alat, t.tingkat_urgensi, t.tgl_lapor
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  WHERE t.status_ticket = 'Open'
                  ORDER BY t.tgl_lapor DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get priority/emergency work orders (Staf IPSRS)
    public function getWorkOrderPriority($limit = 5) {
        $query = "SELECT t.id_ticket, t.id_aset, a.kode_label, a.nama_alat, t.tingkat_urgensi, t.tgl_lapor, t.status_ticket
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  WHERE t.tingkat_urgensi IN ('Tinggi', 'Darurat')
                  ORDER BY t.tgl_lapor DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get work orders in progress (Staf IPSRS)
    public function getWorkOrderInProgress($limit = 5) {
        $query = "SELECT t.id_ticket, t.id_aset, a.kode_label, a.nama_alat, t.tgl_lapor, u.nama_lengkap
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  LEFT JOIN m_user u ON t.id_teknisi_penanggungjawab = u.id_user
                  WHERE t.status_ticket = 'Dikerjakan'
                  ORDER BY t.tgl_lapor DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get work orders by reporter (Unit Pengguna)
    public function getWorkOrderByReporter($id_user_pelapor, $limit = 5) {
        $query = "SELECT t.id_ticket, t.id_aset, a.kode_label, a.nama_alat, t.status_ticket, t.tgl_lapor
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  WHERE t.id_user_pelapor = :id_user_pelapor
                  ORDER BY t.tgl_lapor DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user_pelapor', $id_user_pelapor, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get unresolved work orders (Unit Pengguna)
    public function getWorkOrderUnresolved($id_user_pelapor, $limit = 5) {
        $query = "SELECT t.id_ticket, t.id_aset, a.kode_label, a.nama_alat, t.status_ticket, t.tgl_lapor
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  WHERE t.id_user_pelapor = :id_user_pelapor
                    AND t.status_ticket != 'Closed'
                  ORDER BY t.tgl_lapor DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user_pelapor', $id_user_pelapor, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get recently updated work orders (Unit Pengguna)
    public function getWorkOrderRecentUpdate($id_user_pelapor, $limit = 3) {
        $query = "SELECT t.id_ticket, t.id_aset, a.kode_label, a.nama_alat, t.status_ticket, t.tgl_lapor
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  WHERE t.id_user_pelapor = :id_user_pelapor
                  ORDER BY t.tgl_lapor DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user_pelapor', $id_user_pelapor, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get completed work orders (Unit Pengguna)
    public function getWorkOrderCompleted($id_user_pelapor, $limit = 3) {
        $query = "SELECT t.id_ticket, t.id_aset, a.kode_label, a.nama_alat, t.tgl_lapor
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  WHERE t.id_user_pelapor = :id_user_pelapor
                    AND t.status_ticket = 'Closed'
                  ORDER BY t.tgl_lapor DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':id_user_pelapor', $id_user_pelapor, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get work orders for this week (for calendar)
    public function getWorkOrdersMingguIni() {
        $query = "
            SELECT DATE(t.tgl_lapor) as tanggal,
                   COUNT(*) as total_wo
            FROM t_troubleshoot t
            WHERE DATE(t.tgl_lapor) >= CURDATE()
              AND DATE(t.tgl_lapor) < DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(t.tgl_lapor)
            ORDER BY DATE(t.tgl_lapor) ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get work orders for a specific date
    public function getWorkOrdersByDate($tanggal) {
        $query = "SELECT t.id_ticket, t.id_aset, a.kode_label, a.nama_alat, t.tingkat_urgensi, t.status_ticket, u.nama_lengkap, t.tgl_lapor
                  FROM t_troubleshoot t
                  LEFT JOIN m_aset a ON t.id_aset = a.id_aset
                  LEFT JOIN m_user u ON t.id_teknisi_penanggungjawab = u.id_user
                  WHERE DATE(t.tgl_lapor) = :tanggal
                  ORDER BY t.tgl_lapor ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['tanggal' => $tanggal]);
        return $stmt->fetchAll();
    }
}