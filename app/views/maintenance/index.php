<style>
body {
    font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
</style>

<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff;">
    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: #ecfdf5; color: #047857; font-size: 14px; font-family: 'Nunito', sans-serif;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- STATISTICS CARDS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 24px;">
        <div style="padding: 16px; background: #eff6ff; border-radius: 12px; border-left: 4px solid #3d6aff;">
            <div style="font-size: 12px; color: #8e9bb0; margin-bottom: 6px; font-family: 'Nunito', sans-serif; font-weight: 500;">Total Item</div>
            <div style="font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                <?= $data['statistik']->total_items ?? 0; ?>
            </div>
        </div>

        <div style="padding: 16px; background: #fef3c7; border-radius: 12px; border-left: 4px solid #f59e0b;">
            <div style="font-size: 12px; color: #8e9bb0; margin-bottom: 6px; font-family: 'Nunito', sans-serif; font-weight: 500;">Hari Ini</div>
            <div style="font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                <?= $data['statistik']->hari_ini ?? 0; ?>
            </div>
        </div>

        <div style="padding: 16px; background: #dbeafe; border-radius: 12px; border-left: 4px solid #0ea5e9;">
            <div style="font-size: 12px; color: #8e9bb0; margin-bottom: 6px; font-family: 'Nunito', sans-serif; font-weight: 500;">Selesai Hari Ini</div>
            <div style="font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                <?= $data['statistik']->sudah_selesai_hari_ini ?? 0; ?>
            </div>
        </div>

        <div style="padding: 16px; background: #f3f4f6; border-radius: 12px; border-left: 4px solid #6b7280;">
            <div style="font-size: 12px; color: #8e9bb0; margin-bottom: 6px; font-family: 'Nunito', sans-serif; font-weight: 500;">Bulan Ini</div>
            <div style="font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                <?= $data['statistik']->bulan_ini ?? 0; ?>
            </div>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div style="margin-bottom: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="<?= BASEURL; ?>/maintenance/log" style="padding: 11px 18px; background: #3d6aff; color: #ffffff; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; transition: all 0.2s; display: inline-block;" onmouseover="this.style.background='#2952cc'" onmouseout="this.style.background='#3d6aff'">
            + Input Log
        </a>
        <a href="<?= BASEURL; ?>/maintenance/history" style="padding: 11px 18px; background: #e5e7eb; color: #1a2b56; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; transition: all 0.2s; display: inline-block;" onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
            📋 Riwayat
        </a>
    </div>

    <!-- JADWAL HARI INI -->
    <div style="margin-bottom: 24px;">
        <h2 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #1a2b56; font-family: 'Nunito', sans-serif;">
            📅 Jadwal Hari Ini
        </h2>

        <?php if (!empty($data['pending_hari_ini'])): ?>
            <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
                <?php foreach ($data['pending_hari_ini'] as $i => $item): ?>
                    <div style="padding: 14px 16px; border-bottom: <?= ($i < count($data['pending_hari_ini']) - 1) ? '1px solid #e5e7eb' : 'none'; ?>; display: flex; justify-content: space-between; align-items: center; gap: 12px;">
                        <div style="flex: 1;">
                            <div style="font-size: 14px; font-weight: 600; color: #1a2b56; margin-bottom: 4px; font-family: 'Nunito', sans-serif;">
                                <?= escape($item->nama_item); ?>
                            </div>
                            <div style="font-size: 12px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
                                <?= str_replace('_', ' ', escape($item->frekuensi)); ?> • <?= escape($item->lokasi ?? 'Lokasi umum'); ?>
                            </div>
                        </div>
                        <div>
                            <?php if ($item->sudah_dikerjakan > 0): ?>
                                <div style="padding: 6px 10px; background: #ecfdf5; color: #047857; border-radius: 8px; font-size: 12px; font-weight: 600; font-family: 'Nunito', sans-serif;">
                                    ✓ <?= $item->sudah_dikerjakan; ?>x
                                </div>
                            <?php else: ?>
                                <div style="padding: 6px 10px; background: #fef2f2; color: #991b1b; border-radius: 8px; font-size: 12px; font-weight: 600; font-family: 'Nunito', sans-serif;">
                                    ⏱ Pending
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="padding: 32px 16px; text-align: center; color: #8e9bb0; border-radius: 12px; background: #f9fafb;">
                <div style="font-size: 14px; font-family: 'Nunito', sans-serif;">Tidak ada jadwal maintenance hari ini</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- LOG TERBARU -->
    <div>
        <h2 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #1a2b56; font-family: 'Nunito', sans-serif;">
            📝 Log Terbaru
        </h2>

        <?php if (!empty($data['recent_logs'])): ?>
            <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
                <?php foreach ($data['recent_logs'] as $i => $log): ?>
                    <div style="padding: 14px 16px; border-bottom: <?= ($i < count($data['recent_logs']) - 1) ? '1px solid #e5e7eb' : 'none'; ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px; margin-bottom: 8px;">
                            <div style="flex: 1;">
                                <div style="font-size: 14px; font-weight: 600; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                                    <?= escape($log->nama_item); ?>
                                </div>
                                <div style="font-size: 12px; color: #8e9bb0; margin-top: 2px; font-family: 'Nunito', sans-serif;">
                                    <?= date('d/m/Y H:i', strtotime($log->tgl_rencana)); ?> • <?= escape($log->nama_lengkap ?? 'System'); ?>
                                </div>
                            </div>
                            <?php
                            $statusBg = [
                                'Terselesaikan' => '#ecfdf5',
                                'Terjadwal' => '#fef3c7',
                                'Tertunda' => '#fef2f2',
                                'Dibatalkan' => '#f3f4f6'
                            ];
                            $statusColor = [
                                'Terselesaikan' => '#047857',
                                'Terjadwal' => '#b45309',
                                'Tertunda' => '#991b1b',
                                'Dibatalkan' => '#4b5563'
                            ];
                            $statusLabel = [
                                'Terselesaikan' => '✓ Selesai',
                                'Terjadwal' => '⏳ Terjadwal',
                                'Tertunda' => '⚠ Tertunda',
                                'Dibatalkan' => '✕ Dibatalkan'
                            ];
                            $status = $log->status_pelaksanaan;
                            ?>
                            <div style="padding: 5px 10px; background: <?= $statusBg[$status] ?? '#f3f4f6'; ?>; color: <?= $statusColor[$status] ?? '#4b5563'; ?>; border-radius: 6px; font-size: 11px; font-weight: 600; font-family: 'Nunito', sans-serif; white-space: nowrap;">
                                <?= $statusLabel[$status] ?? 'Unknown'; ?>
                            </div>
                        </div>
                        <div style="font-size: 12px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
                            Kondisi: <strong style="color: #1a2b56;"><?= escape($log->kondisi_laporan ?? 'Normal'); ?></strong>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="padding: 32px 16px; text-align: center; color: #8e9bb0; border-radius: 12px; background: #f9fafb;">
                <div style="font-size: 14px; font-family: 'Nunito', sans-serif;">Belum ada log maintenance</div>
            </div>
        <?php endif; ?>
    </div>
</div>
