<?php
// File: app/models/Aset_model.php

class Aset_model {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->connect();
    }

    // 🔹 GET ALL ASET
    public function getAllAset() {
        $query = "SELECT 
                    a.*,
                    r.nama_ruang
                  FROM m_aset a
                  LEFT JOIN m_ruangan r ON a.id_ruang_saat_ini = r.id_ruang
                  ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 🔹 GET ASET BY RUANGAN
    public function getAsetByRuangan($id_ruang) {
        $query = "SELECT 
                    a.*,
                    r.nama_ruang
                  FROM m_aset a
                  LEFT JOIN m_ruangan r ON a.id_ruang_saat_ini = r.id_ruang
                  WHERE a.id_ruang_saat_ini = :id_ruang
                  ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id_ruang' => $id_ruang]);
        return $stmt->fetchAll();
    }

    // 🔹 GET ALL ASET BASIC (UNTUK GENERATE QR MASSAL)
    public function getAllAsetBasic() {
        $query = "SELECT id_aset, kode_label, nama_alat, file_qr_code
                  FROM m_aset
                  ORDER BY id_aset ASC";

        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 🔥 GET FILTERED ASET
    public function getFilteredAset($search = '', $kategori = '', $kondisi = '') {
        $query = "SELECT 
                    a.*,
                    r.nama_ruang
                FROM m_aset a
                LEFT JOIN m_ruangan r ON a.id_ruang_saat_ini = r.id_ruang
                WHERE 1=1";

        $params = [];

        if (!empty($search)) {
            $query .= " AND (a.nama_alat LIKE :search_nama OR a.kode_label LIKE :search_kode)";
            $params['search_nama'] = '%' . $search . '%';
            $params['search_kode'] = '%' . $search . '%';
        }

        if (!empty($kategori)) {
            $query .= " AND a.kategori_aset = :kategori";
            $params['kategori'] = $kategori;
        }

        if (!empty($kondisi)) {
            $query .= " AND a.status_kondisi = :kondisi";
            $params['kondisi'] = $kondisi;
        }

        $query .= " ORDER BY a.created_at DESC";

        $stmt = $this->db->prepare($query);
        $stmt->execute($params);

        return $stmt->fetchAll();
    }
    
    // 🔹 GET BY ID
    public function getAsetById($id) {
        $query = "SELECT 
                    a.*,
                    r.nama_ruang
                  FROM m_aset a
                  LEFT JOIN m_ruangan r ON a.id_ruang_saat_ini = r.id_ruang
                  WHERE a.id_aset = :id
                  LIMIT 1";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id' => $id]);
        return $stmt->fetch();
    }

    // 🔹 GET ALL RUANGAN
    public function getAllRuangan() {
        $query = "SELECT id_ruang, nama_ruang FROM m_ruangan ORDER BY nama_ruang ASC";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // 🔹 CREATE
    public function createAset($data) {
        $query = "INSERT INTO m_aset (
                    kode_label,
                    nama_alat,
                    kategori_aset,
                    jumlah_unit,
                    merk,
                    model,
                    serial_number,
                    no_sertifikat,
                    tgl_pengadaan,
                    tgl_kalibrasi_terakhir,
                    harga_perolehan,
                    status_kondisi,
                    id_ruang_saat_ini,
                    lokasi_fisik,
                    keterangan,
                    gambar_aset,
                    latitude,
                    longitude,
                    created_at,
                    updated_at
                  ) VALUES (
                    :kode_label,
                    :nama_alat,
                    :kategori_aset,
                    :jumlah_unit,
                    :merk,
                    :model,
                    :serial_number,
                    :no_sertifikat,
                    :tgl_pengadaan,
                    :tgl_kalibrasi_terakhir,
                    :harga_perolehan,
                    :status_kondisi,
                    :id_ruang_saat_ini,
                    :lokasi_fisik,
                    :keterangan,
                    :gambar_aset,
                    :latitude,
                    :longitude,
                    NOW(),
                    NOW()
                  )";

        $stmt = $this->db->prepare($query);

        $success = $stmt->execute([
            'kode_label' => $data['kode_label'],
            'nama_alat' => $data['nama_alat'],
            'kategori_aset' => $data['kategori_aset'],
            'jumlah_unit' => $data['jumlah_unit'],
            'merk' => $data['merk'],
            'model' => $data['model'],
            'serial_number' => $data['serial_number'],
            'no_sertifikat' => $data['no_sertifikat'],
            'tgl_pengadaan' => $data['tgl_pengadaan'] ?: null,
            'tgl_kalibrasi_terakhir' => $data['tgl_kalibrasi_terakhir'] ?: null,
            'harga_perolehan' => ($data['harga_perolehan'] !== '' && $data['harga_perolehan'] !== null) ? $data['harga_perolehan'] : null,
            'status_kondisi' => $data['status_kondisi'],
            'id_ruang_saat_ini' => !empty($data['id_ruang_saat_ini']) ? $data['id_ruang_saat_ini'] : null,
            'lokasi_fisik' => $data['lokasi_fisik'] ?? '',
            'keterangan' => $data['keterangan'],
            'gambar_aset' => $data['gambar_aset'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null
        ]);

        if ($success) {
            return (int) $this->db->lastInsertId();
        }

        return false;
    }

    // 🔹 UPDATE
    public function updateAset($id, $data) {
        $query = "UPDATE m_aset SET
                    kode_label = :kode_label,
                    nama_alat = :nama_alat,
                    kategori_aset = :kategori_aset,
                    jumlah_unit = :jumlah_unit,
                    merk = :merk,
                    model = :model,
                    serial_number = :serial_number,
                    no_sertifikat = :no_sertifikat,
                    tgl_pengadaan = :tgl_pengadaan,
                    tgl_kalibrasi_terakhir = :tgl_kalibrasi_terakhir,
                    harga_perolehan = :harga_perolehan,
                    status_kondisi = :status_kondisi,
                    id_ruang_saat_ini = :id_ruang_saat_ini,
                    lokasi_fisik = :lokasi_fisik,
                    keterangan = :keterangan,
                    gambar_aset = :gambar_aset,
                    latitude = :latitude,
                    longitude = :longitude,
                    updated_at = NOW()
                  WHERE id_aset = :id";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'kode_label' => $data['kode_label'],
            'nama_alat' => $data['nama_alat'],
            'kategori_aset' => $data['kategori_aset'],
            'jumlah_unit' => $data['jumlah_unit'],
            'merk' => $data['merk'],
            'model' => $data['model'],
            'serial_number' => $data['serial_number'],
            'no_sertifikat' => $data['no_sertifikat'],
            'tgl_pengadaan' => $data['tgl_pengadaan'] ?: null,
            'tgl_kalibrasi_terakhir' => $data['tgl_kalibrasi_terakhir'] ?: null,
            'harga_perolehan' => ($data['harga_perolehan'] !== '' && $data['harga_perolehan'] !== null) ? $data['harga_perolehan'] : null,
            'status_kondisi' => $data['status_kondisi'],
            'id_ruang_saat_ini' => !empty($data['id_ruang_saat_ini']) ? $data['id_ruang_saat_ini'] : null,
            'lokasi_fisik' => $data['lokasi_fisik'] ?? '',
            'keterangan' => $data['keterangan'],
            'gambar_aset' => $data['gambar_aset'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null
        ]);
    }

    // 🔹 UPDATE QR CODE
    public function updateQRCode($id, $fileName) {
        $query = "UPDATE m_aset
                  SET file_qr_code = :file_qr_code,
                      updated_at = NOW()
                  WHERE id_aset = :id";

        $stmt = $this->db->prepare($query);
        return $stmt->execute([
            'id' => $id,
            'file_qr_code' => $fileName
        ]);
    }

    // 🔹 DELETE
    public function deleteAset($id) {
        $query = "DELETE FROM m_aset WHERE id_aset = :id";
        $stmt = $this->db->prepare($query);
        return $stmt->execute(['id' => $id]);
    }

    // 🔹 COUNT ALL
    public function countAllAset() {
        $query = "SELECT COUNT(*) as total FROM m_aset";
        $stmt = $this->db->prepare($query);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row ? (int)$row->total : 0;
    }

    // 🔹 COUNT ASET BY RUANGAN
    public function countAsetByRuangan($id_ruang) {
        $query = "SELECT COUNT(*) as total
                  FROM m_aset
                  WHERE id_ruang_saat_ini = :id_ruang";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['id_ruang' => $id_ruang]);
        $row = $stmt->fetch();
        return $row ? (int)$row->total : 0;
    }

    // 🔹 COUNT BY KONDISI
    public function countByKondisi($kondisi) {
        $query = "SELECT COUNT(*) as total FROM m_aset WHERE status_kondisi = :kondisi";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['kondisi' => $kondisi]);
        $row = $stmt->fetch();
        return $row ? (int)$row->total : 0;
    }

    // 🔹 COUNT BY KONDISI DAN RUANGAN
    public function countByKondisiAndRuangan($kondisi, $id_ruang) {
        $query = "SELECT COUNT(*) as total
                  FROM m_aset
                  WHERE status_kondisi = :kondisi
                  AND id_ruang_saat_ini = :id_ruang";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'kondisi' => $kondisi,
            'id_ruang' => $id_ruang
        ]);
        $row = $stmt->fetch();
        return $row ? (int)$row->total : 0;
    }

    // 🔹 COUNT BY KATEGORI
    public function countByKategori($kategori) {
        $query = "SELECT COUNT(*) as total FROM m_aset WHERE kategori_aset = :kategori";
        $stmt = $this->db->prepare($query);
        $stmt->execute(['kategori' => $kategori]);
        $row = $stmt->fetch();
        return $row ? (int)$row->total : 0;
    }

    // 🔹 COUNT BY KATEGORI DAN RUANGAN
    public function countByKategoriAndRuangan($kategori, $id_ruang) {
        $query = "SELECT COUNT(*) as total
                  FROM m_aset
                  WHERE kategori_aset = :kategori
                  AND id_ruang_saat_ini = :id_ruang";
        $stmt = $this->db->prepare($query);
        $stmt->execute([
            'kategori' => $kategori,
            'id_ruang' => $id_ruang
        ]);
        $row = $stmt->fetch();
        return $row ? (int)$row->total : 0;
    }

    // 🔹 ASET PER RUANGAN
    public function getAsetPerRuangan($limit = 8) {
        $query = "SELECT 
                    r.nama_ruang,
                    COUNT(a.id_aset) AS total_aset
                  FROM m_ruangan r
                  LEFT JOIN m_aset a ON a.id_ruang_saat_ini = r.id_ruang
                  GROUP BY r.id_ruang, r.nama_ruang
                  ORDER BY total_aset DESC, r.nama_ruang ASC
                  LIMIT :limit";

        $stmt = $this->db->prepare($query);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    // 🔹 ASET PER RUANGAN KHUSUS 1 RUANG
    public function getAsetPerRuanganById($id_ruang) {
        $query = "SELECT 
                    r.nama_ruang,
                    COUNT(a.id_aset) AS total_aset
                  FROM m_ruangan r
                  LEFT JOIN m_aset a ON a.id_ruang_saat_ini = r.id_ruang
                  WHERE r.id_ruang = :id_ruang
                  GROUP BY r.id_ruang, r.nama_ruang";

        $stmt = $this->db->prepare($query);
        $stmt->execute(['id_ruang' => $id_ruang]);

        return $stmt->fetchAll();
    }
}