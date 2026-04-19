<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:15px; flex-wrap:wrap;">
        <div>
            <h3 style="margin:0;">Reminder Kalibrasi</h3>
            <p style="margin:8px 0 0; color:#666;">Aset medis yang perlu diperiksa ulang kalibrasinya.</p>
        </div>
        <a href="<?= BASEURL; ?>/aset" style="padding:10px 16px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px;">Kembali ke Aset</a>
    </div>

    <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:1000px;">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="padding:12px; text-align:left;">Kode</th>
                    <th style="padding:12px; text-align:left;">Nama Alat</th>
                    <th style="padding:12px; text-align:left;">Ruangan</th>
                    <th style="padding:12px; text-align:left;">Kalibrasi Terakhir</th>
                    <th style="padding:12px; text-align:left;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['aset'])): ?>
                    <?php foreach ($data['aset'] as $aset): ?>
                        <tr>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?= escape($aset->kode_label ?? '-') ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?= escape($aset->nama_alat ?? '-') ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?= escape($aset->nama_ruang ?? '-') ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?= escape($aset->tgl_kalibrasi_terakhir ?? 'Belum ada data') ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;">
                                <a href="<?= BASEURL; ?>/aset/edit/<?= $aset->id_aset; ?>" style="padding:8px 12px; background:#ffc107; color:#222; text-decoration:none; border-radius:8px;">Update Kalibrasi</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" style="padding:20px; text-align:center; color:#666;">Tidak ada aset yang masuk reminder kalibrasi.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
