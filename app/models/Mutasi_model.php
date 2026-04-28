<?php

class Mutasi_model {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // Get mutasi by reporter/requester (Unit RS)
    public function getMutasiByUser($user_id) {
        $query = "
            SELECT m.*,
                   a.kode_label, a.nama_alat,
                   r1.nama_ruang as ruang_asal_nama,
                   r2.nama_ruang as ruang_tujuan_nama,
                   u.nama_lengkap
            FROM t_mutasi m
            JOIN m_aset a ON m.id_aset = a.id_aset
            JOIN m_ruangan r1 ON m.ruang_asal = r1.id_ruang
            JOIN m_ruangan r2 ON m.ruang_tujuan = r2.id_ruang
            LEFT JOIN m_user u ON m.id_user_pencatat = u.id_user
            WHERE m.id_user_pencatat = :user_id
            ORDER BY m.tgl_mutasi DESC
            LIMIT 100";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['user_id' => $user_id]);
        return $stmt->fetchAll();
    }

    // Ambil semua mutasi dengan filter
    public function getAllMutasi($status = null) {
        $query = "
            SELECT m.*,
                   a.kode_label, a.nama_alat,
                   r1.nama_ruang as ruang_asal_nama,
                   r2.nama_ruang as ruang_tujuan_nama,
                   u.nama_lengkap
            FROM t_mutasi m
            JOIN m_aset a ON m.id_aset = a.id_aset
            JOIN m_ruangan r1 ON m.ruang_asal = r1.id_ruang
            JOIN m_ruangan r2 ON m.ruang_tujuan = r2.id_ruang
            LEFT JOIN m_user u ON m.id_user_pencatat = u.id_user
            WHERE 1=1";

        $params = [];

        if ($status) {
            $query .= " AND m.status_mutasi = :status";
            $params['status'] = $status;
        }

        $query .= " ORDER BY m.tgl_mutasi DESC LIMIT 100";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Ambil detail mutasi by ID
    public function getMutasiById($id) {
        $query = "
            SELECT m.*,
                   a.kode_label, a.nama_alat, a.id_ruang_saat_ini,
                   r1.nama_ruang as ruang_asal_nama,
                   r2.nama_ruang as ruang_tujuan_nama,
                   u.nama_lengkap
            FROM t_mutasi m
            JOIN m_aset a ON m.id_aset = a.id_aset
            JOIN m_ruangan r1 ON m.ruang_asal = r1.id_ruang
            JOIN m_ruangan r2 ON m.ruang_tujuan = r2.id_ruang
            LEFT JOIN m_user u ON m.id_user_pencatat = u.id_user
            WHERE m.id_mutasi = :id
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // Buat mutasi baru
    public function createMutasi($data) {
        $query = "
            INSERT INTO t_mutasi
            (id_aset, ruang_asal, ruang_tujuan, id_user_pencatat, alasan_mutasi, status_mutasi, catatan)
            VALUES
            (:id_aset, :ruang_asal, :ruang_tujuan, :id_user_pencatat, :alasan_mutasi, :status_mutasi, :catatan)
        ";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id_aset' => $data['id_aset'],
            'ruang_asal' => $data['ruang_asal'],
            'ruang_tujuan' => $data['ruang_tujuan'],
            'id_user_pencatat' => $data['id_user_pencatat'],
            'alasan_mutasi' => $data['alasan_mutasi'],
            'status_mutasi' => $data['status_mutasi'] ?? 'Menunggu_Verifikasi',
            'catatan' => $data['catatan'] ?? ''
        ]);
    }

    // Update status mutasi
    public function updateStatus($id_mutasi, $status) {
        $query = "UPDATE t_mutasi SET status_mutasi = :status WHERE id_mutasi = :id_mutasi";
        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id_mutasi' => $id_mutasi,
            'status' => $status
        ]);
    }

    // Selesaikan mutasi (update lokasi aset + ubah status)
    public function selesaikanMutasi($id_mutasi) {
        // Get mutasi details
        $mutasi = $this->getMutasiById($id_mutasi);
        if (!$mutasi) {
            return false;
        }

        // Update aset location
        $updateQuery = "UPDATE m_aset SET id_ruang_saat_ini = :ruang_tujuan WHERE id_aset = :id_aset";
        $stmt = $this->db->prepare($updateQuery);

        if ($stmt->execute([
            'ruang_tujuan' => $mutasi->ruang_tujuan,
            'id_aset' => $mutasi->id_aset
        ])) {
            // Update mutasi status to Selesai
            return $this->updateStatus($id_mutasi, 'Selesai');
        }

        return false;
    }

    // Delete mutasi
    public function deleteMutasi($id_mutasi) {
        $query = "DELETE FROM t_mutasi WHERE id_mutasi = :id_mutasi";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id_mutasi' => $id_mutasi]);
    }

    // Statistik mutasi
    public function getStatistik() {
        $query = "
            SELECT
                COUNT(*) as total,
                (SELECT COUNT(*) FROM t_mutasi WHERE status_mutasi = 'Menunggu_Verifikasi') as menunggu,
                (SELECT COUNT(*) FROM t_mutasi WHERE status_mutasi = 'Disetujui') as disetujui,
                (SELECT COUNT(*) FROM t_mutasi WHERE status_mutasi = 'Selesai' AND DATE(tgl_mutasi) = CURDATE()) as selesai_hari_ini
            FROM t_mutasi
        ";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetch();
    }

    // 📍 SIDEBAR METHODS - STAF LOGISTIK

    // Get recent transfers (Staf Logistik)
    public function getMutasiTerbaru($limit = 5) {
        $query = "SELECT m.id_mutasi, m.id_aset, a.kode_label, a.nama_alat, r1.nama_ruang as asal, r2.nama_ruang as tujuan, m.status_mutasi, m.tgl_mutasi
                  FROM t_mutasi m
                  JOIN m_aset a ON m.id_aset = a.id_aset
                  JOIN m_ruangan r1 ON m.ruang_asal = r1.id_ruang
                  JOIN m_ruangan r2 ON m.ruang_tujuan = r2.id_ruang
                  ORDER BY m.tgl_mutasi DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get assets in/out (Staf Logistik)
    public function getMutasiKelMasuk($limit = 5) {
        $query = "SELECT m.id_mutasi, a.kode_label, a.nama_alat, r1.nama_ruang as asal, r2.nama_ruang as tujuan, m.status_mutasi
                  FROM t_mutasi m
                  JOIN m_aset a ON m.id_aset = a.id_aset
                  JOIN m_ruangan r1 ON m.ruang_asal = r1.id_ruang
                  JOIN m_ruangan r2 ON m.ruang_tujuan = r2.id_ruang
                  WHERE m.status_mutasi IN ('Menunggu_Verifikasi', 'Disetujui')
                  ORDER BY m.tgl_mutasi DESC
                  LIMIT :limit";
        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
