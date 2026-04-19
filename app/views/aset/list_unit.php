<?php
$aset = $data['aset'] ?? [];
$namaRuang = $data['nama_ruang'] ?? 'Ruangan';
?>

<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff;">

    <!-- HEADER -->
    <div style="margin-bottom: 24px;">
        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">Daftar Aset di Ruangan</h1>
        <p style="margin: 0; color: #8e9bb0; font-size: 14px; font-family: 'Nunito', sans-serif;">
            Menampilkan semua peralatan medis & sarana prasarana di <strong><?= escape($namaRuang); ?></strong>
        </p>
    </div>

    <!-- TABLE/LIST -->
    <?php if (!empty($aset)): ?>
        <div style="overflow-x: auto; border-radius: 12px; border: 1px solid #e5e7eb;">
            <table style="width: 100%; border-collapse: collapse; min-width: 1200px; font-family: 'Nunito', sans-serif;">
                <thead style="background: #f9fafb; border-bottom: 1px solid #e5e7eb;">
                    <tr>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #1a2b56; font-size: 13px;">No</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #1a2b56; font-size: 13px;">Kode Label</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #1a2b56; font-size: 13px;">Nama Alat</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #1a2b56; font-size: 13px;">Kategori</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #1a2b56; font-size: 13px;">Merk & Model</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #1a2b56; font-size: 13px;">Status</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600; color: #1a2b56; font-size: 13px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($aset as $a): ?>
                        <?php
                        // Warna status
                        $statusColor = '#4b5563';
                        $statusBgColor = '#f3f4f6';
                        if ($a->status_kondisi === 'Baik') {
                            $statusColor = '#047857';
                            $statusBgColor = '#d1fae5';
                        } elseif ($a->status_kondisi === 'Rusak Ringan') {
                            $statusColor = '#c2410c';
                            $statusBgColor = '#fed7aa';
                        } elseif ($a->status_kondisi === 'Rusak Berat') {
                            $statusColor = '#991b1b';
                            $statusBgColor = '#fee2e2';
                        } elseif ($a->status_kondisi === 'Maintenance') {
                            $statusColor = '#0369a1';
                            $statusBgColor = '#dbeafe';
                        } elseif ($a->status_kondisi === 'Gudang') {
                            $statusColor = '#6b21a8';
                            $statusBgColor = '#f3e8ff';
                        }
                        ?>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <td style="padding: 12px; color: #1a2b56; font-size: 13px;"><?= $no++; ?></td>
                            <td style="padding: 12px; font-weight: 600; color: #1a2b56; font-size: 13px;">
                                <?= escape($a->kode_label ?? '-'); ?>
                            </td>
                            <td style="padding: 12px; color: #1a2b56; font-size: 13px;">
                                <?= escape($a->nama_alat ?? '-'); ?>
                            </td>
                            <td style="padding: 12px;">
                                <span style="display: inline-block; padding: 4px 10px; background: #eff6ff; color: #0369a1; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                    <?= escape($a->kategori_aset ?? 'Umum'); ?>
                                </span>
                            </td>
                            <td style="padding: 12px; font-size: 13px; color: #1a2b56;">
                                <div><?= escape($a->merk ?? '-'); ?></div>
                                <div style="color: #8e9bb0; font-size: 12px;"><?= escape($a->model ?? '-'); ?></div>
                            </td>
                            <td style="padding: 12px;">
                                <span style="display: inline-block; padding: 6px 12px; background: <?= $statusBgColor; ?>; color: <?= $statusColor; ?>; border-radius: 8px; font-size: 12px; font-weight: 600;">
                                    <?= escape($a->status_kondisi ?? 'Tidak Diketahui'); ?>
                                </span>
                            </td>
                            <td style="padding: 12px;">
                                <a href="<?= BASEURL; ?>/aset/detail/<?= $a->id_aset; ?>"
                                   style="display: inline-block; padding: 6px 12px; background: #3d6aff; color: white; text-decoration: none; border-radius: 8px; font-size: 12px; font-weight: 600; font-family: 'Nunito', sans-serif; transition: all 0.2s;"
                                   onmouseover="this.style.background='#2952cc'"
                                   onmouseout="this.style.background='#3d6aff'">
                                    Lihat Detail
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- SUMMARY -->
        <div style="margin-top: 20px; padding: 16px; background: #f9fafb; border-radius: 12px; font-family: 'Nunito', sans-serif; font-size: 14px; color: #1a2b56;">
            <strong>Total Aset:</strong> <?= count($aset); ?> unit
            | <strong>Baik:</strong> <?php echo count(array_filter($aset, fn($a) => $a->status_kondisi === 'Baik')); ?>
            | <strong>Rusak:</strong> <?php echo count(array_filter($aset, fn($a) => in_array($a->status_kondisi, ['Rusak Ringan', 'Rusak Berat']))); ?>
            | <strong>Maintenance:</strong> <?php echo count(array_filter($aset, fn($a) => $a->status_kondisi === 'Maintenance')); ?>
        </div>

    <?php else: ?>
        <div style="padding: 48px 16px; text-align: center; color: #8e9bb0;">
            <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
            <p style="margin: 0; font-size: 16px; font-weight: 500; font-family: 'Nunito', sans-serif;">Tidak ada aset di ruangan ini</p>
        </div>
    <?php endif; ?>

</div>
