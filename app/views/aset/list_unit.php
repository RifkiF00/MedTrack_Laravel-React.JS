<?php
$aset = $data['aset'] ?? [];
$namaRuang = $data['nama_ruang'] ?? 'Ruangan';
?>

<div class="card" style="padding: 24px;">

    <!-- HEADER -->
    <div style="margin-bottom: 24px;">
        <h3 style="margin: 0 0 8px 0;">Daftar Aset di Ruangan</h3>
        <p style="margin: 0; color: #666; font-size: 14px;">
            Menampilkan semua peralatan medis & sarana prasarana di <strong><?= escape($namaRuang); ?></strong>
        </p>
    </div>

    <!-- TABLE/LIST -->
    <?php if (!empty($aset)): ?>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse; min-width: 1200px;">
                <thead style="background: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <tr>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">No</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Kode Label</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Nama Alat</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Kategori</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Merk & Model</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($aset as $a): ?>
                        <?php
                        // Warna status
                        $statusColor = '#e5e7eb';
                        $statusBgColor = '#f3f4f6';
                        if ($a['status_kondisi'] === 'Baik') {
                            $statusColor = '#059669';
                            $statusBgColor = '#d1fae5';
                        } elseif ($a['status_kondisi'] === 'Rusak Ringan') {
                            $statusColor = '#ea580c';
                            $statusBgColor = '#fed7aa';
                        } elseif ($a['status_kondisi'] === 'Rusak Berat') {
                            $statusColor = '#dc2626';
                            $statusBgColor = '#fee2e2';
                        } elseif ($a['status_kondisi'] === 'Maintenance') {
                            $statusColor = '#2563eb';
                            $statusBgColor = '#dbeafe';
                        } elseif ($a['status_kondisi'] === 'Gudang') {
                            $statusColor = '#7c3aed';
                            $statusBgColor = '#ede9fe';
                        }
                        ?>
                        <tr style="border-bottom: 1px solid #eee;">
                            <td style="padding: 12px;"><?= $no++; ?></td>
                            <td style="padding: 12px; font-weight: 600; color: #0f172a;">
                                <?= escape($a['kode_label'] ?? '-'); ?>
                            </td>
                            <td style="padding: 12px;">
                                <?= escape($a['nama_alat'] ?? '-'); ?>
                            </td>
                            <td style="padding: 12px;">
                                <span style="display: inline-block; padding: 4px 8px; background: #f0f9ff; color: #0369a1; border-radius: 6px; font-size: 12px; font-weight: 500;">
                                    <?= escape($a['kategori_aset'] ?? 'Umum'); ?>
                                </span>
                            </td>
                            <td style="padding: 12px; font-size: 13px;">
                                <div><?= escape($a['merk'] ?? '-'); ?></div>
                                <div style="color: #666;"><?= escape($a['model'] ?? '-'); ?></div>
                            </td>
                            <td style="padding: 12px;">
                                <span style="display: inline-block; padding: 6px 12px; background: <?= $statusBgColor; ?>; color: <?= $statusColor; ?>; border-radius: 999px; font-size: 12px; font-weight: 600;">
                                    <?= escape($a['status_kondisi'] ?? 'Tidak Diketahui'); ?>
                                </span>
                            </td>
                            <td style="padding: 12px;">
                                <a href="<?= BASEURL; ?>/aset/detail/<?= $a['id_aset']; ?>"
                                   style="display: inline-block; padding: 6px 12px; background: #0d6efd; color: white; text-decoration: none; border-radius: 6px; font-size: 12px;">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- SUMMARY -->
        <div style="margin-top: 20px; padding: 16px; background: #f8f9fa; border-radius: 8px;">
            <strong>Total Aset:</strong> <?= count($aset); ?> unit
            | <strong>Baik:</strong> <?php echo count(array_filter($aset, fn($a) => $a['status_kondisi'] === 'Baik')); ?>
            | <strong>Rusak:</strong> <?php echo count(array_filter($aset, fn($a) => in_array($a['status_kondisi'], ['Rusak Ringan', 'Rusak Berat']))); ?>
            | <strong>Maintenance:</strong> <?php echo count(array_filter($aset, fn($a) => $a['status_kondisi'] === 'Maintenance')); ?>
        </div>

    <?php else: ?>
        <div style="padding: 40px; text-align: center; color: #999;">
            <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
            <p style="margin: 0;">Tidak ada aset di ruangan ini</p>
        </div>
    <?php endif; ?>

</div>
