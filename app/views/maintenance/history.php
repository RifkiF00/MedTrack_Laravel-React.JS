<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff;">
    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: #ecfdf5; color: #047857; font-size: 14px; font-family: 'Nunito', sans-serif;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- FILTER & BUTTON -->
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap; align-items: center;">
        <select id="filterItem" style="padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 10px; background: #ffffff; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; transition: all 0.2s;" onfocus="this.style.borderColor='#3d6aff'" onblur="this.style.borderColor='#e5e7eb'">
            <option value="">-- Semua Item --</option>
            <?php foreach ($data['items'] as $item): ?>
                <option value="<?= $item->id_pemeliharaan; ?>">
                    <?= escape($item->nama_item); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <select id="filterStatus" style="padding: 10px 12px; border: 1px solid #e5e7eb; border-radius: 10px; background: #ffffff; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; transition: all 0.2s;" onfocus="this.style.borderColor='#3d6aff'" onblur="this.style.borderColor='#e5e7eb'">
            <option value="">-- Semua Status --</option>
            <option value="Terselesaikan">✓ Terselesaikan</option>
            <option value="Terjadwal">⏳ Terjadwal</option>
            <option value="Tertunda">⚠ Tertunda</option>
            <option value="Dibatalkan">✕ Dibatalkan</option>
        </select>

        <a href="<?= BASEURL; ?>/maintenance/log" style="margin-left: auto; padding: 10px 16px; background: #3d6aff; color: #ffffff; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; transition: all 0.2s; display: inline-block;" onmouseover="this.style.background='#2952cc'" onmouseout="this.style.background='#3d6aff'">
            + Input Baru
        </a>
    </div>

    <!-- TABLE / LIST -->
    <?php if (!empty($data['logs'])): ?>
        <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
            <?php foreach ($data['logs'] as $i => $log): ?>
                <div data-item="<?= $log->id_pemeliharaan; ?>" data-status="<?= $log->status_pelaksanaan; ?>" style="padding: 14px 16px; border-bottom: <?= ($i < count($data['logs']) - 1) ? '1px solid #e5e7eb' : 'none'; ?>; transition: background 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background=''">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px; margin-bottom: 8px;">
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #1a2b56; margin-bottom: 2px; font-family: 'Nunito', sans-serif;">
                                <?= escape($log->nama_item); ?>
                            </div>
                            <div style="font-size: 12px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
                                <?= date('d/m/Y H:i', strtotime($log->tgl_rencana)); ?> • <?= escape($log->nama_lengkap ?? 'System'); ?>
                            </div>
                        </div>
                        <?php
                        $statusConfig = [
                            'Terselesaikan' => ['bg' => '#ecfdf5', 'color' => '#047857', 'label' => '✓ Selesai'],
                            'Terjadwal' => ['bg' => '#fef3c7', 'color' => '#b45309', 'label' => '⏳ Terjadwal'],
                            'Tertunda' => ['bg' => '#fef2f2', 'color' => '#991b1b', 'label' => '⚠ Tertunda'],
                            'Dibatalkan' => ['bg' => '#f3f4f6', 'color' => '#4b5563', 'label' => '✕ Dibatalkan']
                        ];
                        $config = $statusConfig[$log->status_pelaksanaan] ?? $statusConfig['Terjadwal'];
                        ?>
                        <div style="padding: 5px 10px; background: <?= $config['bg']; ?>; color: <?= $config['color']; ?>; border-radius: 6px; font-size: 11px; font-weight: 600; font-family: 'Nunito', sans-serif; white-space: nowrap;">
                            <?= $config['label']; ?>
                        </div>
                    </div>

                    <div style="display: flex; gap: 16px; font-size: 12px;">
                        <div>
                            <span style="color: #8e9bb0; font-family: 'Nunito', sans-serif;">Kondisi:</span>
                            <?php
                            $kondisiConfig = [
                                'Normal' => ['bg' => '#ecfdf5', 'color' => '#047857'],
                                'Perlu Perbaikan' => ['bg' => '#fef3c7', 'color' => '#b45309'],
                                'Rusak' => ['bg' => '#fef2f2', 'color' => '#991b1b'],
                                'Penggantian Part' => ['bg' => '#dbeafe', 'color' => '#0369a1']
                            ];
                            $kconfig = $kondisiConfig[$log->kondisi_laporan] ?? $kondisiConfig['Normal'];
                            ?>
                            <span style="display: inline-block; margin-left: 4px; padding: 3px 8px; background: <?= $kconfig['bg']; ?>; color: <?= $kconfig['color']; ?>; border-radius: 5px; font-weight: 600; font-family: 'Nunito', sans-serif;">
                                <?= escape($log->kondisi_laporan ?? 'Normal'); ?>
                            </span>
                        </div>
                        <?php if ($log->hasil_pengecekan): ?>
                            <div style="color: #8e9bb0; font-family: 'Nunito', sans-serif;">
                                Catatan: <strong style="color: #1a2b56; font-family: 'Nunito', sans-serif;">
                                    <?= escape(strlen($log->hasil_pengecekan) > 50 ? substr($log->hasil_pengecekan, 0, 50) . '...' : $log->hasil_pengecekan); ?>
                                </strong>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="padding: 48px 16px; text-align: center; color: #8e9bb0; border-radius: 12px; background: #f9fafb;">
            <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px; font-family: 'Nunito', sans-serif;">Belum ada riwayat maintenance</div>
            <div style="font-size: 14px; font-family: 'Nunito', sans-serif;">
                <a href="<?= BASEURL; ?>/maintenance/log" style="color: #3d6aff; text-decoration: none;">
                    Mulai dengan menginput log maintenance
                </a>
            </div>
        </div>
    <?php endif; ?>
</div>

<script>
document.getElementById('filterItem')?.addEventListener('change', filterList);
document.getElementById('filterStatus')?.addEventListener('change', filterList);

function filterList() {
    const itemFilter = document.getElementById('filterItem')?.value || '';
    const statusFilter = document.getElementById('filterStatus')?.value || '';
    const rows = document.querySelectorAll('[data-item]');

    rows.forEach(row => {
        let show = true;
        if (itemFilter && row.getAttribute('data-item') !== itemFilter) {
            show = false;
        }
        if (statusFilter && row.getAttribute('data-status') !== statusFilter) {
            show = false;
        }
        row.style.display = show ? '' : 'none';
    });
}
</script>
