<?php

class Maintenance_model {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Ambil semua item pemeliharaan yang aktif
    public function getAllPemeliharaan() {
        $query = "SELECT * FROM m_pemeliharaan WHERE status = 'Aktif' ORDER BY nama_item ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Ambil detail satu item pemeliharaan
    public function getPemeliharaanById($id) {
        $query = "SELECT * FROM m_pemeliharaan WHERE id_pemeliharaan = :id";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Ambil jadwal pemeliharaan untuk bulan ini
    public function getJadwalBulanIni() {
        $query = "
            SELECT p.*,
                   COALESCE(COUNT(l.id_log), 0) as total_log
            FROM m_pemeliharaan p
            LEFT JOIN t_pemeliharaan_log l ON p.id_pemeliharaan = l.id_pemeliharaan
                AND MONTH(l.tgl_rencana) = MONTH(CURDATE())
                AND YEAR(l.tgl_rencana) = YEAR(CURDATE())
            WHERE p.status = 'Aktif'
            GROUP BY p.id_pemeliharaan, p.nama_item, p.deskripsi, p.lokasi, p.frekuensi,
                     p.pic_penanggung_jawab, p.catatan, p.status, p.created_at, p.updated_at
            ORDER BY p.nama_item ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Ambil log pemeliharaan dengan filter
    public function getLogPemeliharaan($id_pemeliharaan = null, $bulan = null, $tahun = null) {
        $query = "SELECT l.*, m.nama_item, u.nama_lengkap
                  FROM t_pemeliharaan_log l
                  JOIN m_pemeliharaan m ON l.id_pemeliharaan = m.id_pemeliharaan
                  LEFT JOIN m_user u ON l.id_user_pelaksana = u.id_user
                  WHERE 1=1";

        $params = [];

        if ($id_pemeliharaan) {
            $query .= " AND l.id_pemeliharaan = :id_pemeliharaan";
            $params['id_pemeliharaan'] = $id_pemeliharaan;
        }
        if ($bulan && $tahun) {
            $query .= " AND MONTH(l.tgl_rencana) = :bulan AND YEAR(l.tgl_rencana) = :tahun";
            $params['bulan'] = $bulan;
            $params['tahun'] = $tahun;
        }

        $query .= " ORDER BY l.tgl_rencana DESC LIMIT 50";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Ambil jadwal untuk minggu ini (untuk kalender)
    public function getJadwalMingguIni() {
        $query = "
            SELECT DATE(l.tgl_rencana) as tanggal,
                   COUNT(*) as total_jadwal
            FROM t_pemeliharaan_log l
            WHERE DATE(l.tgl_rencana) >= CURDATE()
              AND DATE(l.tgl_rencana) < DATE_ADD(CURDATE(), INTERVAL 7 DAY)
            GROUP BY DATE(l.tgl_rencana)
            ORDER BY DATE(l.tgl_rencana) ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Ambil jadwal yang belum dikerjakan hari ini
    public function getJadwalPendingHariIni() {
        $query = "
            SELECT p.*,
                   (SELECT COUNT(*) FROM t_pemeliharaan_log
                    WHERE id_pemeliharaan = p.id_pemeliharaan
                    AND DATE(tgl_rencana) = CURDATE()
                    AND status_pelaksanaan = 'Terselesaikan') as sudah_dikerjakan
            FROM m_pemeliharaan p
            WHERE p.status = 'Aktif'
            ORDER BY p.nama_item ASC
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Buat log pemeliharaan baru
    public function createLog($data) {
        $query = "
            INSERT INTO t_pemeliharaan_log
            (id_pemeliharaan, id_user_pelaksana, tgl_rencana, status_pelaksanaan, hasil_pengecekan, kondisi_laporan, catatan_khusus)
            VALUES
            (:id_pemeliharaan, :id_user_pelaksana, :tgl_rencana, :status_pelaksanaan, :hasil_pengecekan, :kondisi_laporan, :catatan_khusus)
        ";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id_pemeliharaan' => $data['id_pemeliharaan'],
            'id_user_pelaksana' => $data['id_user_pelaksana'],
            'tgl_rencana' => $data['tgl_rencana'],
            'status_pelaksanaan' => $data['status_pelaksanaan'],
            'hasil_pengecekan' => $data['hasil_pengecekan'],
            'kondisi_laporan' => $data['kondisi_laporan'],
            'catatan_khusus' => $data['catatan_khusus']
        ]);
    }

    // Update status log
    public function updateLogStatus($id_log, $status) {
        $query = "UPDATE t_pemeliharaan_log SET status_pelaksanaan = :status WHERE id_log = :id_log";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id_log' => $id_log,
            'status' => $status
        ]);
    }

    // Statistik maintenance
    public function getStatistik() {
        $query = "
            SELECT
                (SELECT COUNT(*) FROM m_pemeliharaan WHERE status = 'Aktif') as total_items,
                (SELECT COUNT(*) FROM t_pemeliharaan_log WHERE DATE(tgl_rencana) = CURDATE()) as hari_ini,
                (SELECT COUNT(*) FROM t_pemeliharaan_log WHERE MONTH(tgl_rencana) = MONTH(CURDATE()) AND YEAR(tgl_rencana) = YEAR(CURDATE())) as bulan_ini,
                (SELECT COUNT(*) FROM t_pemeliharaan_log WHERE status_pelaksanaan = 'Terselesaikan' AND DATE(tgl_rencana) = CURDATE()) as sudah_selesai_hari_ini
        ";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    // 📍 SIDEBAR METHODS - STAF IPSRS

    // Get upcoming calibration (Staf IPSRS)
    public function getKalibrasiMendekati($limit = 5) {
        $query = "SELECT p.id_pemeliharaan, p.nama_item, p.lokasi, COUNT(l.id_log) as total_log
                  FROM m_pemeliharaan p
                  LEFT JOIN t_pemeliharaan_log l ON p.id_pemeliharaan = l.id_pemeliharaan
                    AND DATE(l.tgl_rencana) <= DATE_ADD(CURDATE(), INTERVAL 7 DAY)
                  WHERE p.status = 'Aktif'
                    AND p.frekuensi IN ('6_Bulanan', 'Tahunan')
                  GROUP BY p.id_pemeliharaan
                  ORDER BY p.nama_item ASC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get today's scheduled maintenance (Staf IPSRS)
    public function getMaintenanceHariIni() {
        $query = "SELECT p.id_pemeliharaan, p.nama_item, p.lokasi, l.status_pelaksanaan, u.nama_lengkap
                  FROM t_pemeliharaan_log l
                  JOIN m_pemeliharaan p ON l.id_pemeliharaan = p.id_pemeliharaan
                  LEFT JOIN m_user u ON l.id_user_pelaksana = u.id_user
                  WHERE DATE(l.tgl_rencana) = CURDATE()
                  ORDER BY l.tgl_rencana ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get maintenance schedule for a specific date
    public function getMaintenanceByDate($tanggal) {
        $query = "SELECT l.id_log, p.id_pemeliharaan, p.nama_item, p.lokasi, l.status_pelaksanaan, u.nama_lengkap, l.tgl_rencana
                  FROM t_pemeliharaan_log l
                  JOIN m_pemeliharaan p ON l.id_pemeliharaan = p.id_pemeliharaan
                  LEFT JOIN m_user u ON l.id_user_pelaksana = u.id_user
                  WHERE DATE(l.tgl_rencana) = :tanggal
                  ORDER BY l.tgl_rencana ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['tanggal' => $tanggal]);
        return $stmt->fetchAll();
    }

    // Create new maintenance item
    public function createPemeliharaan($data) {
        $query = "INSERT INTO m_pemeliharaan (
                    nama_item,
                    deskripsi,
                    lokasi,
                    frekuensi,
                    pic_penanggung_jawab,
                    catatan,
                    status
                  ) VALUES (
                    :nama_item,
                    :deskripsi,
                    :lokasi,
                    :frekuensi,
                    :pic_penanggung_jawab,
                    :catatan,
                    :status
                  )";

        $stmt = $this->db->prepare($query);

        return $stmt->execute([
            'nama_item' => $data['nama_item'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'lokasi' => $data['lokasi'] ?? null,
            'frekuensi' => $data['frekuensi'],
            'pic_penanggung_jawab' => $data['pic_penanggung_jawab'] ?? null,
            'catatan' => $data['catatan'] ?? null,
            'status' => 'Aktif'
        ]);
    }

    // Get maintenance item by name
    public function getPemeliharaanByName($nama_item) {
        $query = "SELECT * FROM m_pemeliharaan WHERE nama_item = :nama_item LIMIT 1";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['nama_item' => $nama_item]);
        return $stmt->fetch();
    }
}