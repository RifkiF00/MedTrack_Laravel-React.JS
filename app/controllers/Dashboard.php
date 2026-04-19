<?php

class Dashboard extends Controller {

    private function guard() {
        if (!isset($_SESSION['user_id'])) {
            header('Location: ' . BASEURL . '/auth');
            exit;
        }
    }

    public function index() {
        $this->guard();

        $asetModel = $this->model('Aset_model');

        $role = $_SESSION['role'] ?? 'User';
        $nama = $_SESSION['nama_lengkap'] ?? 'User';
        $idRuang = $_SESSION['id_ruang'] ?? null;

        $data['role'] = $role;
        $data['nama'] = $nama;

        // Default
        $data['total_aset'] = 0;
        $data['total_medis'] = 0;
        $data['total_sarpras'] = 0;
        $data['total_it'] = 0;

        $data['baik'] = 0;
        $data['rusak_ringan'] = 0;
        $data['rusak_berat'] = 0;
        $data['maintenance'] = 0;
        $data['gudang'] = 0;

        $data['ruangan_labels'] = [];
        $data['ruangan_totals'] = [];

        // Jika role Unit_RS, tampilkan hanya aset di ruangannya
        if ($role === 'Unit_RS' && !empty($idRuang)) {

            $data['total_aset'] = $asetModel->countAsetByRuangan($idRuang);

            $data['total_medis'] = $asetModel->countByKategoriAndRuangan('Medis', $idRuang);
            $data['total_sarpras'] = $asetModel->countByKategoriAndRuangan('Sarpras', $idRuang);
            $data['total_it'] = $asetModel->countByKategoriAndRuangan('IT', $idRuang);

            $data['baik'] = $asetModel->countByKondisiAndRuangan('Baik', $idRuang);
            $data['rusak_ringan'] = $asetModel->countByKondisiAndRuangan('Rusak Ringan', $idRuang);
            $data['rusak_berat'] = $asetModel->countByKondisiAndRuangan('Rusak Berat', $idRuang);
            $data['maintenance'] = $asetModel->countByKondisiAndRuangan('Maintenance', $idRuang);
            $data['gudang'] = $asetModel->countByKondisiAndRuangan('Gudang', $idRuang);

            $ruanganStats = $asetModel->getAsetPerRuanganById($idRuang);

            $data['ruangan_labels'] = array_map(function($item) {
                return $item->nama_ruang;
            }, $ruanganStats);

            $data['ruangan_totals'] = array_map(function($item) {
                return (int)$item->total_aset;
            }, $ruanganStats);

            $data['page_subheading'] = 'Ringkasan data aset pada ruangan/unit Anda.';
        } else {
            // Staf IPSRS & Staf Logistik melihat semua data
            $data['total_aset'] = $asetModel->countAllAset();

            $data['total_medis'] = $asetModel->countByKategori('Medis');
            $data['total_sarpras'] = $asetModel->countByKategori('Sarpras');
            $data['total_it'] = $asetModel->countByKategori('IT');

            $data['baik'] = $asetModel->countByKondisi('Baik');
            $data['rusak_ringan'] = $asetModel->countByKondisi('Rusak Ringan');
            $data['rusak_berat'] = $asetModel->countByKondisi('Rusak Berat');
            $data['maintenance'] = $asetModel->countByKondisi('Maintenance');
            $data['gudang'] = $asetModel->countByKondisi('Gudang');

            $ruanganStats = $asetModel->getAsetPerRuangan(8);

            $data['ruangan_labels'] = array_map(function($item) {
                return $item->nama_ruang;
            }, $ruanganStats);

            $data['ruangan_totals'] = array_map(function($item) {
                return (int)$item->total_aset;
            }, $ruanganStats);

            $data['page_subheading'] = 'Ringkasan data aset dan kondisi alat.';
        }

        $data['judul'] = 'Dashboard - MedTrack IPSRS';
        $data['page_heading'] = 'Dashboard';
        $data['content_view'] = 'dashboard/index';

        $this->view('templates/dashboard_layout', $data);
    }
}