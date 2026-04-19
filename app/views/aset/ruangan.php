<div class="card">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:15px; flex-wrap:wrap;">
        <div>
            <h3 style="margin:0;">Ringkasan Aset per Ruangan</h3>
            <p style="margin:8px 0 0; color:#666;">Rekap jumlah item, unit, dan kondisi aset per ruangan.</p>
        </div>
        <a href="<?= BASEURL; ?>/aset" style="padding:10px 16px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px;">Kembali ke Aset</a>
    </div>

    <div style="overflow:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:1100px;">
            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="padding:12px; text-align:left;">Ruangan</th>
                    <th style="padding:12px; text-align:left;">Total Item</th>
                    <th style="padding:12px; text-align:left;">Total Unit</th>
                    <th style="padding:12px; text-align:left;">Baik</th>
                    <th style="padding:12px; text-align:left;">Maintenance</th>
                    <th style="padding:12px; text-align:left;">Bermasalah</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($data['ringkasan'])): ?>
                    <?php foreach ($data['ringkasan'] as $row): ?>
                        <tr>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?= escape($row->nama_ruang) ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?= (int)$row->total_item ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?= (int)$row->total_unit ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?= (int)$row->aset_baik ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?= (int)$row->aset_maintenance ?></td>
                            <td style="padding:12px; border-bottom:1px solid #eee;"><?= (int)$row->aset_bermasalah ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" style="padding:20px; text-align:center; color:#666;">Belum ada ringkasan per ruangan.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
