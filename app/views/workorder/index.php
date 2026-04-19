<?php
$flash = $data['flash'] ?? null;
$workorders = $data['workorders'] ?? [];
$role = $_SESSION['role'] ?? '';
$teknisiList = $data['teknisi_list'] ?? [];
?>

<div class="card">

    <!-- HEADER -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; gap:15px; flex-wrap:wrap;">
        <div>
            <h1 style="margin:0; font-size:28px; font-weight:700; color:#1a2b56; font-family:'Nunito',sans-serif;">Daftar Work Order</h1>
            <p style="margin:8px 0 0; color:#8e9bb0; font-family:'Nunito',sans-serif; font-size:14px;">
                <?= ($role === 'Staf_IPSRS' || $role === 'Staf_Logistik')
                    ? 'Daftar laporan kerusakan untuk ditindaklanjuti.'
                    : 'Riwayat laporan kerusakan yang Anda buat.'; ?>
            </p>
        </div>

        <?php if ($role === 'Unit_RS'): ?>
            <div>
                <a href="<?= BASEURL; ?>/workorder/create"
                   style="padding:11px 18px; background:#3d6aff; color:#fff; text-decoration:none; border-radius:10px; font-size:14px; font-weight:600; font-family:'Nunito',sans-serif; transition:all 0.2s;"
                   onmouseover="this.style.background='#2952cc'"
                   onmouseout="this.style.background='#3d6aff'">
                    + Buat Work Order
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- FLASH -->
    <?php if ($flash): ?>
        <div style="margin-bottom:20px; padding:12px 16px; border-radius:12px; background:#ecfdf5; color:#047857; font-size:14px; font-family:'Nunito',sans-serif;">
            ✓ <?= escape($flash['message']); ?>
        </div>
    <?php endif; ?>

    <!-- TABLE -->
    <div style="overflow-x:auto; border-radius:12px; border:1px solid #e5e7eb;">
        <table style="width:100%; border-collapse:collapse; min-width:1600px; font-family:'Nunito',sans-serif;">
            <thead style="background:#f9fafb;">
                <tr>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">No</th>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Tanggal</th>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Kode</th>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Nama Aset</th>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Ruangan</th>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Kerusakan</th>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Foto</th>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Urgensi</th>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Status</th>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Pelapor</th>
                    <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Teknisi</th>

                    <?php if ($role === 'Staf_IPSRS'): ?>
                        <th style="padding:12px; text-align:left; font-size:13px; font-weight:600; color:#1a2b56; border-bottom:1px solid #e5e7eb;">Aksi</th>
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
                        $urgensiTextColor = '#4b5563';
                        if ($urgensi === 'Rendah') {
                            $urgensiColor = '#dbeafe';
                            $urgensiTextColor = '#0369a1';
                        } elseif ($urgensi === 'Sedang') {
                            $urgensiColor = '#fef3c7';
                            $urgensiTextColor = '#b45309';
                        } elseif ($urgensi === 'Tinggi') {
                            $urgensiColor = '#fee2e2';
                            $urgensiTextColor = '#991b1b';
                        } elseif ($urgensi === 'Darurat') {
                            $urgensiColor = '#fecaca';
                            $urgensiTextColor = '#dc2626';
                        }

                        // warna status
                        $statusColor = '#e5e7eb';
                        $statusTextColor = '#4b5563';
                        if ($status === 'Open') {
                            $statusColor = '#fef3c7';
                            $statusTextColor = '#b45309';
                        } elseif ($status === 'Pengecekan') {
                            $statusColor = '#dbeafe';
                            $statusTextColor = '#0369a1';
                        } elseif ($status === 'Dikerjakan') {
                            $statusColor = '#fde68a';
                            $statusTextColor = '#ca8a04';
                        } elseif ($status === 'Closed') {
                            $statusColor = '#d1fae5';
                            $statusTextColor = '#047857';
                        }
                        ?>

                        <tr style="border-bottom:1px solid #e5e7eb;">
                            <td style="padding:12px; font-size:13px; color:#1a2b56;"><?= $no++; ?></td>

                            <td style="padding:12px; font-size:13px; color:#1a2b56;">
                                <?= !empty($wo['tgl_lapor']) ? date('d/m/Y H:i', strtotime($wo['tgl_lapor'])) : '-'; ?>
                            </td>

                            <td style="padding:12px; font-size:13px; color:#1a2b56;"><?= escape($wo['kode_label'] ?? '-'); ?></td>
                            <td style="padding:12px; font-size:13px; color:#1a2b56;"><?= escape($wo['nama_alat'] ?? '-'); ?></td>
                            <td style="padding:12px; font-size:13px; color:#1a2b56;"><?= escape($wo['nama_ruang'] ?? '-'); ?></td>
                            <td style="padding:12px; font-size:13px; color:#1a2b56;"><?= escape(substr($wo['deskripsi_kerusakan'] ?? '-', 0, 40)); ?></td>

                            <!-- FOTO -->
                            <td style="padding:12px;">
                                <?php if (!empty($wo['foto_kerusakan'])): ?>
                                    <a href="<?= BASEURL; ?>/uploads/troubleshoot/<?= escape($wo['foto_kerusakan']); ?>" target="_blank">
                                        <img src="<?= BASEURL; ?>/uploads/troubleshoot/<?= escape($wo['foto_kerusakan']); ?>"
                                             style="width:44px; height:44px; object-fit:cover; border-radius:8px;">
                                    </a>
                                <?php else: ?>
                                    <span style="color:#999; font-size:13px;">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- URGENSI -->
                            <td style="padding:12px;">
                                <span style="padding:6px 10px; border-radius:8px; background:<?= $urgensiColor ?>; color:<?= $urgensiTextColor ?>; font-size:12px; font-weight:600;">
                                    <?= escape($urgensi); ?>
                                </span>
                            </td>

                            <!-- STATUS -->
                            <td style="padding:12px;">
                                <span style="padding:6px 10px; border-radius:8px; background:<?= $statusColor ?>; color:<?= $statusTextColor ?>; font-size:12px; font-weight:600;">
                                    <?= escape($status); ?>
                                </span>
                            </td>

                            <td style="padding:12px; font-size:13px; color:#1a2b56;"><?= escape($wo['nama_pelapor'] ?? '-'); ?></td>
                            <td style="padding:12px; font-size:13px; color:#1a2b56;"><?= escape($wo['nama_teknisi'] ?? '-'); ?></td>

                            <!-- 🔥 AKSI IPSRS -->
                            <?php if ($role === 'Staf_IPSRS'): ?>
                                <td style="padding:12px; min-width:240px;">

                                    <!-- ASSIGN TEKNISI -->
                                    <form method="POST" action="<?= BASEURL; ?>/workorder/assignTeknisi/<?= $wo['id_ticket']; ?>" style="margin-bottom:8px;">
                                        <input type="hidden" name="csrf_token" value="<?= getCSRFToken(); ?>">

                                        <select name="id_teknisi_penanggungjawab" style="width:100%; padding:8px; margin-bottom:6px; border:1px solid #e5e7eb; border-radius:8px; font-size:12px; font-family:'Nunito',sans-serif;">
                                            <option value="">-- Pilih Teknisi --</option>
                                            <?php foreach ($teknisiList as $t): ?>
                                                <option value="<?= $t['id_user']; ?>"
                                                    <?= (($wo['id_teknisi_penanggungjawab'] ?? null) == $t['id_user']) ? 'selected' : ''; ?>>
                                                    <?= escape($t['nama_lengkap']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>

                                        <button type="submit"
                                                style="width:100%; padding:8px; background:#3d6aff; color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; font-family:'Nunito',sans-serif; cursor:pointer;">
                                            Assign
                                        </button>
                                    </form>

                                    <!-- UPDATE STATUS -->
                                    <form method="POST" action="<?= BASEURL; ?>/workorder/updateStatus/<?= $wo['id_ticket']; ?>">
                                        <input type="hidden" name="csrf_token" value="<?= getCSRFToken(); ?>">

                                        <select name="status_ticket" style="width:100%; padding:8px; margin-bottom:6px; border:1px solid #e5e7eb; border-radius:8px; font-size:12px; font-family:'Nunito',sans-serif;">
                                            <option value="Open" <?= $status === 'Open' ? 'selected' : '' ?>>Open</option>
                                            <option value="Pengecekan" <?= $status === 'Pengecekan' ? 'selected' : '' ?>>Pengecekan</option>
                                            <option value="Dikerjakan" <?= $status === 'Dikerjakan' ? 'selected' : '' ?>>Dikerjakan</option>
                                            <option value="Closed" <?= $status === 'Closed' ? 'selected' : '' ?>>Closed</option>
                                        </select>

                                        <input type="text"
                                               name="catatan_status"
                                               placeholder="Catatan..."
                                               style="width:100%; padding:8px; margin-bottom:6px; border:1px solid #e5e7eb; border-radius:8px; font-size:12px; font-family:'Nunito',sans-serif;">

                                        <button type="submit"
                                                style="width:100%; padding:8px; background:#0ea5e9; color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:600; font-family:'Nunito',sans-serif; cursor:pointer;">
                                            Update
                                        </button>
                                    </form>

                                </td>
                            <?php endif; ?>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="12" style="text-align:center; padding:24px; color:#8e9bb0; font-size:14px; font-family:'Nunito',sans-serif;">
                            Belum ada Work Order
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</div>