<div class="card" style="padding: 24px;">
    <div style="margin-bottom: 24px;">
        <h3 style="margin: 0 0 8px 0;">Riwayat Pemeliharaan</h3>
        <p style="margin: 0; color: #666; font-size: 14px;">
            Histori lengkap pelaksanaan maintenance rutin
        </p>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 14px; border-radius: 8px; background: #eaf7ee; color: #1f7a3d;">
            <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- FILTER & SEARCH -->
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <select id="filterItem" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; background: #fff; font-size: 14px;">
            <option value="">-- Semua Item --</option>
            <?php foreach ($data['items'] as $item): ?>
                <option value="<?= $item->id_pemeliharaan; ?>">
                    <?= escape($item->nama_item); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="filterStatus" style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; background: #fff; font-size: 14px;">
            <option value="">-- Semua Status --</option>
            <option value="Terselesaikan">✓ Terselesaikan</option>
            <option value="Terjadwal">⏳ Terjadwal</option>
            <option value="Tertunda">⚠ Tertunda</option>
            <option value="Dibatalkan">✕ Dibatalkan</option>
        </select>

        <a href="<?= BASEURL; ?>/maintenance/log" style="padding: 10px 16px; background: #0d6efd; color: #fff; text-decoration: none; border-radius: 6px; font-size: 14px;">
            + Input Log Baru
        </a>
    </div>

    <!-- TABLE -->
    <?php if (!empty($data['logs'])): ?>
        <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
            <thead>
                <tr style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Item Maintenance</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Tanggal</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Pelaksana</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Status</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Kondisi</th>
                    <th style="padding: 12px; text-align: left; font-weight: 600;">Catatan</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($data['logs'] as $log): ?>
                    <tr style="border-bottom: 1px solid #dee2e6; hover-background: #f9f9f9;" onmouseover="this.style.background='#f9f9f9'" onmouseout="this.style.background=''">
                        <td style="padding: 12px; color: #333; font-weight: 500;">
                            <?= escape($log->nama_item); ?>
                        </td>
                        <td style="padding: 12px; color: #666;">
                            <?= date('d/m/Y H:i', strtotime($log->tgl_rencana)); ?>
                        </td>
                        <td style="padding: 12px; color: #666;">
                            <?= escape($log->nama_lengkap ?? 'System'); ?>
                        </td>
                        <td style="padding: 12px;">
                            <?php
                            $statusColors = [
                                'Terselesaikan' => ['bg' => '#d4edda', 'color' => '#155724', 'label' => '✓ Selesai'],
                                'Terjadwal' => ['bg' => '#fff3cd', 'color' => '#856404', 'label' => '⏳ Terjadwal'],
                                'Tertunda' => ['bg' => '#f8d7da', 'color' => '#721c24', 'label' => '⚠ Tertunda'],
                                'Dibatalkan' => ['bg' => '#e2e3e5', 'color' => '#383d41', 'label' => '✕ Dibatalkan']
                            ];
                            $status = $statusColors[$log->status_pelaksanaan] ?? $statusColors['Terjadwal'];
                            ?>
                            <span style="display: inline-block; padding: 4px 8px; background: <?= $status['bg']; ?>; color: <?= $status['color']; ?>; border-radius: 4px; font-size: 12px; font-weight: 500;">
                                <?= $status['label']; ?>
                            </span>
                        </td>
                        <td style="padding: 12px;">
                            <?php
                            $kondisiColors = [
                                'Normal' => ['bg' => '#d4edda', 'color' => '#155724'],
                                'Perlu Perbaikan' => ['bg' => '#fff3cd', 'color' => '#856404'],
                                'Rusak' => ['bg' => '#f8d7da', 'color' => '#721c24'],
                                'Penggantian Part' => ['bg' => '#cfe2ff', 'color' => '#084298']
                            ];
                            $kondisi = $kondisiColors[$log->kondisi_laporan] ?? $kondisiColors['Normal'];
                            ?>
                            <span style="display: inline-block; padding: 4px 8px; background: <?= $kondisi['bg']; ?>; color: <?= $kondisi['color']; ?>; border-radius: 4px; font-size: 12px;">
                                <?= escape($log->kondisi_laporan ?? 'Normal'); ?>
                            </span>
                        </td>
                        <td style="padding: 12px; color: #666; font-size: 13px;">
                            <?php
                            $catatan = $log->hasil_pengecekan ?? $log->catatan_khusus ?? '-';
                            $preview = strlen($catatan) > 50 ? substr($catatan, 0, 50) . '...' : $catatan;
                            echo escape($preview);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <div style="padding: 60px 20px; text-align: center; color: #999;">
            <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
            <p style="margin: 0; font-size: 16px;">Belum ada riwayat maintenance</p>
            <p style="margin: 8px 0 0; font-size: 13px; color: #bbb;">
                <a href="<?= BASEURL; ?>/maintenance/log" style="color: #0d6efd; text-decoration: none;">
                    Mulai dengan menginput log maintenance
                </a>
            </p>
        </div>
    <?php endif; ?>
</div>

<script>
// Simple client-side filtering
document.getElementById('filterItem')?.addEventListener('change', function() {
    filterTable();
});
document.getElementById('filterStatus')?.addEventListener('change', function() {
    filterTable();
});

function filterTable() {
    const itemFilter = document.getElementById('filterItem')?.value || '';
    const statusFilter = document.getElementById('filterStatus')?.value || '';
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        let show = true;
        if (itemFilter) {
            show = show && row.getAttribute('data-item') === itemFilter;
        }
        if (statusFilter) {
            show = show && row.getAttribute('data-status') === statusFilter;
        }
        row.style.display = show ? '' : 'none';
    });
}
</script>
