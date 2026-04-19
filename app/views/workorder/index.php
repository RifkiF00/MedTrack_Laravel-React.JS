<?php
$flash = $data['flash'] ?? null;
$workorders = $data['workorders'] ?? [];
$role = $_SESSION['role'] ?? '';
$teknisiList = $data['teknisi_list'] ?? [];
?>

<div class="card">

    <!-- HEADER -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:15px; flex-wrap:wrap;">
        <div>
            <h3 style="margin:0;">Daftar Work Order</h3>
            <p style="margin:8px 0 0; color:#666;">
                <?= ($role === 'Staf_IPSRS' || $role === 'Staf_Logistik')
                    ? 'Daftar laporan kerusakan untuk ditindaklanjuti.'
                    : 'Riwayat laporan kerusakan yang Anda buat.'; ?>
            </p>
        </div>

        <?php if ($role === 'Unit_RS'): ?>
            <div>
                <a href="<?= BASEURL; ?>/workorder/create"
                   style="padding:10px 16px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:8px;">
                    + Buat Work Order
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- FLASH -->
    <?php if ($flash): ?>
        <div style="margin-bottom:20px; padding:14px; border-radius:8px; background:#eaf7ee; color:#1f7a3d;">
            <?= escape($flash['message']); ?>
        </div>
    <?php endif; ?>

    <!-- TABLE -->
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:1600px;">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="padding:12px;">No</th>
                    <th style="padding:12px;">Tanggal</th>
                    <th style="padding:12px;">Kode</th>
                    <th style="padding:12px;">Nama Aset</th>
                    <th style="padding:12px;">Ruangan</th>
                    <th style="padding:12px;">Kerusakan</th>
                    <th style="padding:12px;">Foto</th>
                    <th style="padding:12px;">Urgensi</th>
                    <th style="padding:12px;">Status</th>
                    <th style="padding:12px;">Pelapor</th>
                    <th style="padding:12px;">Teknisi</th>

                    <?php if ($role === 'Staf_IPSRS'): ?>
                        <th style="padding:12px;">Aksi</th>
                    <?php endif; ?>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($workorders)): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($workorders as $wo): ?>
                        <?php
                        $urgensi = $wo['tingkat_urgensi'] ?? 'Sedang';
                        $status = $wo['status_ticket'] ?? 'Open';

                        // warna urgensi
                        $urgensiColor = '#e5e7eb';
                        if ($urgensi === 'Rendah') $urgensiColor = '#dbeafe';
                        elseif ($urgensi === 'Sedang') $urgensiColor = '#fef3c7';
                        elseif ($urgensi === 'Tinggi') $urgensiColor = '#fee2e2';
                        elseif ($urgensi === 'Darurat') $urgensiColor = '#fecaca';

                        // warna status
                        $statusColor = '#e5e7eb';
                        if ($status === 'Open') $statusColor = '#fef3c7';
                        elseif ($status === 'Pengecekan') $statusColor = '#dbeafe';
                        elseif ($status === 'Dikerjakan') $statusColor = '#fde68a';
                        elseif ($status === 'Closed') $statusColor = '#d1fae5';
                        ?>

                        <tr style="border-bottom:1px solid #eee;">
                            <td style="padding:12px;"><?= $no++; ?></td>

                            <td style="padding:12px;">
                                <?= !empty($wo['tgl_lapor']) ? date('d-m-Y H:i', strtotime($wo['tgl_lapor'])) : '-'; ?>
                            </td>

                            <td style="padding:12px;"><?= escape($wo['kode_label'] ?? '-'); ?></td>
                            <td style="padding:12px;"><?= escape($wo['nama_alat'] ?? '-'); ?></td>
                            <td style="padding:12px;"><?= escape($wo['nama_ruang'] ?? '-'); ?></td>
                            <td style="padding:12px;"><?= escape($wo['deskripsi_kerusakan'] ?? '-'); ?></td>

                            <!-- FOTO -->
                            <td style="padding:12px;">
                                <?php if (!empty($wo['foto_kerusakan'])): ?>
                                    <a href="<?= BASEURL; ?>/uploads/troubleshoot/<?= escape($wo['foto_kerusakan']); ?>" target="_blank">
                                        <img src="<?= BASEURL; ?>/uploads/troubleshoot/<?= escape($wo['foto_kerusakan']); ?>"
                                             style="width:60px; height:60px; object-fit:cover; border-radius:8px;">
                                    </a>
                                <?php else: ?>
                                    <span style="color:#999;">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- URGENSI -->
                            <td style="padding:12px;">
                                <span style="padding:5px 10px; border-radius:999px; background:<?= $urgensiColor ?>;">
                                    <?= escape($urgensi); ?>
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td style="padding:12px;">
                                <span style="padding:5px 10px; border-radius:999px; background:<?= $statusColor ?>;">
                                    <?= escape($status); ?>
                                </span>
                            </td>

                            <td style="padding:12px;"><?= escape($wo['nama_pelapor'] ?? '-'); ?></td>
                            <td style="padding:12px;"><?= escape($wo['nama_teknisi'] ?? '-'); ?></td>

                            <!-- 🔥 AKSI IPSRS -->
                            <?php if ($role === 'Staf_IPSRS'): ?>
                                <td style="padding:12px; min-width:240px;">

                                    <!-- ASSIGN TEKNISI -->
                                    <form method="POST" action="<?= BASEURL; ?>/workorder/assignTeknisi/<?= $wo['id_ticket']; ?>" style="margin-bottom:8px;">
                                        <input type="hidden" name="csrf_token" value="<?= getCSRFToken(); ?>">

                                        <select name="id_teknisi_penanggungjawab" style="width:100%; padding:6px; margin-bottom:6px;">
                                            <option value="">-- Pilih Teknisi --</option>
                                            <?php foreach ($teknisiList as $t): ?>
                                                <option value="<?= $t['id_user']; ?>"
                                                    <?= (($wo['id_teknisi_penanggungjawab'] ?? null) == $t['id_user']) ? 'selected' : ''; ?>>
                                                    <?= escape($t['nama_lengkap']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <button type="submit"
                                                style="width:100%; padding:5px; background:#0d6efd; color:#fff; border:none; border-radius:6px;">
                                            Assign
                                        </button>
                                    </form>

                                    <!-- UPDATE STATUS -->
                                    <form method="POST" action="<?= BASEURL; ?>/workorder/updateStatus/<?= $wo['id_ticket']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?= getCSRFToken(); ?>">

                                        <select name="status_ticket" style="width:100%; padding:6px; margin-bottom:6px;">
                                            <option value="Open" <?= $status === 'Open' ? 'selected' : '' ?>>Open</option>
                                            <option value="Pengecekan" <?= $status === 'Pengecekan' ? 'selected' : '' ?>>Pengecekan</option>
                                            <option value="Dikerjakan" <?= $status === 'Dikerjakan' ? 'selected' : '' ?>>Dikerjakan</option>
                                            <option value="Closed" <?= $status === 'Closed' ? 'selected' : '' ?>>Closed</option>
                                        </select>

                                        <input type="text"
                                               name="catatan_status"
                                               placeholder="Catatan..."
                                               style="width:100%; padding:6px; margin-bottom:6px;">

                                        <button type="submit"
                                                style="width:100%; padding:5px; background:#2563eb; color:#fff; border:none; border-radius:6px;">
                                            Update
                                        </button>
                                    </form>

                                </td>
                            <?php endif; ?>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" style="text-align:center; padding:20px;">
                            Belum ada Work Order
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>