<?php

class Maintenance_model {
    private $db;

    public function __construct() {
        $this->db = new Database();
    }

    // Ambil semua item pemeliharaan yang aktif
    public function getAllPemeliharaan() {
        $this->db->query('SELECT * FROM m_pemeliharaan WHERE status = "Aktif" ORDER BY nama_item ASC');
        return $this->db->resultSet();
    }

    // Ambil detail satu item pemeliharaan
    public function getPemeliharaanById($id) {
        $this->db->query('SELECT * FROM m_pemeliharaan WHERE id_pemeliharaan = :id');
        $this->db->bind(':id', $id);
        return $this->db->single();
    }

    // Ambil jadwal pemeliharaan untuk bulan ini
    public function getJadwalBulanIni() {
        $this->db->query('
            SELECT p.*, COUNT(l.id_log) as total_log
            FROM m_pemeliharaan p
            LEFT JOIN t_pemeliharaan_log l ON p.id_pemeliharaan = l.id_pemeliharaan
                AND MONTH(l.tgl_rencana) = MONTH(CURDATE())
                AND YEAR(l.tgl_rencana) = YEAR(CURDATE())
            WHERE p.status = "Aktif"
            GROUP BY p.id_pemeliharaan
            ORDER BY p.nama_item ASC
        ');
        return $this->db->resultSet();
    }

    // Ambil log pemeliharaan dengan filter
    public function getLogPemeliharaan($id_pemeliharaan = null, $bulan = null, $tahun = null) {
        $query = 'SELECT l.*, m.nama_item, u.nama_lengkap
                  FROM t_pemeliharaan_log l
                  JOIN m_pemeliharaan m ON l.id_pemeliharaan = m.id_pemeliharaan
                  LEFT JOIN m_user u ON l.id_user_pelaksana = u.id_user
                  WHERE 1=1';

        if ($id_pemeliharaan) {
            $query .= ' AND l.id_pemeliharaan = :id_pemeliharaan';
        }
        if ($bulan && $tahun) {
            $query .= ' AND MONTH(l.tgl_rencana) = :bulan AND YEAR(l.tgl_rencana) = :tahun';
        }

        $query .= ' ORDER BY l.tgl_rencana DESC LIMIT 50';

        $this->db->query($query);
        if ($id_pemeliharaan) {
            $this->db->bind(':id_pemeliharaan', $id_pemeliharaan);
        }
        if ($bulan && $tahun) {
            $this->db->bind(':bulan', $bulan);
            $this->db->bind(':tahun', $tahun);
        }

        return $this->db->resultSet();
    }

    // Ambil jadwal yang belum dikerjakan hari ini
    public function getJadwalPendingHariIni() {
        $this->db->query('
            SELECT p.*,
                   (SELECT COUNT(*) FROM t_pemeliharaan_log
                    WHERE id_pemeliharaan = p.id_pemeliharaan
                    AND DATE(tgl_rencana) = CURDATE()
                    AND status_pelaksanaan = "Terselesaikan") as sudah_dikerjakan
            FROM m_pemeliharaan p
            WHERE p.status = "Aktif"
            ORDER BY p.nama_item ASC
        ');
        return $this->db->resultSet();
    }

    // Buat log pemeliharaan baru
    public function createLog($data) {
        $this->db->query('
            INSERT INTO t_pemeliharaan_log
            (id_pemeliharaan, id_user_pelaksana, tgl_rencana, status_pelaksanaan, hasil_pengecekan, kondisi_laporan, catatan_khusus)
            VALUES
            (:id_pemeliharaan, :id_user_pelaksana, :tgl_rencana, :status_pelaksanaan, :hasil_pengecekan, :kondisi_laporan, :catatan_khusus)
        ');

        $this->db->bind(':id_pemeliharaan', $data['id_pemeliharaan']);
        $this->db->bind(':id_user_pelaksana', $data['id_user_pelaksana']);
        $this->db->bind(':tgl_rencana', $data['tgl_rencana']);
        $this->db->bind(':status_pelaksanaan', $data['status_pelaksanaan']);
        $this->db->bind(':hasil_pengecekan', $data['hasil_pengecekan']);
        $this->db->bind(':kondisi_laporan', $data['kondisi_laporan']);
        $this->db->bind(':catatan_khusus', $data['catatan_khusus']);

        return $this->db->execute();
    }

    // Update status log
    public function updateLogStatus($id_log, $status) {
        $this->db->query('UPDATE t_pemeliharaan_log SET status_pelaksanaan = :status WHERE id_log = :id_log');
        $this->db->bind(':id_log', $id_log);
        $this->db->bind(':status', $status);
        return $this->db->execute();
    }

    // Statistik maintenance
    public function getStatistik() {
        $this->db->query('
            SELECT
                COUNT(DISTINCT id_pemeliharaan) as total_items,
                (SELECT COUNT(*) FROM t_pemeliharaan_log WHERE DATE(tgl_rencana) = CURDATE()) as hari_ini,
                (SELECT COUNT(*) FROM t_pemeliharaan_log WHERE MONTH(tgl_rencana) = MONTH(CURDATE()) AND YEAR(tgl_rencana) = YEAR(CURDATE())) as bulan_ini,
                (SELECT COUNT(*) FROM t_pemeliharaan_log WHERE status_pelaksanaan = "Terselesaikan" AND DATE(tgl_rencana) = CURDATE()) as sudah_selesai_hari_ini
            FROM m_pemeliharaan
            WHERE status = "Aktif"
        ');
        return $this->db->single();
    }
}
