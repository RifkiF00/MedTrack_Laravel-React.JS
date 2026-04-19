<div class="card" style="padding: 24px;">
    <div style="margin-bottom: 24px;">
        <h3 style="margin: 0 0 8px 0;">Preventive Maintenance</h3>
        <p style="margin: 0; color: #666; font-size: 14px;">
            Jadwal dan log pemeliharaan rutin aset untuk menjaga kondisi optimal
        </p>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 14px; border-radius: 8px; background: #eaf7ee; color: #1f7a3d;">
            <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- STATISTICS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
        <div style="padding: 16px; background: #f0f4ff; border-radius: 8px; border-left: 4px solid #0d6efd;">
            <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Total Item</div>
            <div style="font-size: 28px; font-weight: 600; color: #0d6efd;">
                <?= $data['statistik']->total_items ?? 0; ?>
            </div>
        </div>
        <div style="padding: 16px; background: #fff3cd; border-radius: 8px; border-left: 4px solid #ffc107;">
            <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Jadwal Hari Ini</div>
            <div style="font-size: 28px; font-weight: 600; color: #ffc107;">
                <?= $data['statistik']->hari_ini ?? 0; ?>
            </div>
        </div>
        <div style="padding: 16px; background: #d1ecf1; border-radius: 8px; border-left: 4px solid #17a2b8;">
            <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Sudah Selesai Hari Ini</div>
            <div style="font-size: 28px; font-weight: 600; color: #17a2b8;">
                <?= $data['statistik']->sudah_selesai_hari_ini ?? 0; ?>
            </div>
        </div>
        <div style="padding: 16px; background: #e2e3e5; border-radius: 8px; border-left: 4px solid #6c757d;">
            <div style="font-size: 12px; color: #666; margin-bottom: 4px;">Bulan Ini</div>
            <div style="font-size: 28px; font-weight: 600; color: #6c757d;">
                <?= $data['statistik']->bulan_ini ?? 0; ?>
            </div>
        </div>
    </div>

    <!-- BUTTONS -->
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="<?= BASEURL; ?>/maintenance/log" style="padding: 10px 16px; background: #0d6efd; color: #fff; text-decoration: none; border-radius: 8px;">
            + Input Log Maintenance
        </a>
        <a href="<?= BASEURL; ?>/maintenance/history" style="padding: 10px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 8px;">
            📋 Riwayat
        </a>
    </div>

    <!-- PENDING TODAY -->
    <div style="margin-bottom: 24px;">
        <h4 style="margin: 0 0 16px 0; color: #333;">📅 Jadwal Hari Ini</h4>
        <?php if (!empty($data['pending_hari_ini'])): ?>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Item Maintenance</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Frekuensi</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Lokasi</th>
                        <th style="padding: 12px; text-align: center; font-weight: 600;">Sudah Dikerjakan</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['pending_hari_ini'] as $item): ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #333;"><?= escape($item->nama_item); ?></td>
                            <td style="padding: 12px; color: #666;">
                                <?= str_replace('_', ' ', escape($item->frekuensi)); ?>
                            </td>
                            <td style="padding: 12px; color: #666;"><?= escape($item->lokasi ?? '-'); ?></td>
                            <td style="padding: 12px; text-align: center;">
                                <?php if ($item->sudah_dikerjakan > 0): ?>
                                    <span style="display: inline-block; padding: 4px 8px; background: #d4edda; color: #155724; border-radius: 4px; font-size: 12px;">
                                        ✓ <?= $item->sudah_dikerjakan; ?>x
                                    </span>
                                <?php else: ?>
                                    <span style="display: inline-block; padding: 4px 8px; background: #f8d7da; color: #721c24; border-radius: 4px; font-size: 12px;">
                                        ⏱ Pending
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="padding: 40px 20px; text-align: center; color: #999;">
                <p style="margin: 0;">Tidak ada jadwal maintenance hari ini</p>
            </div>
        <?php endif; ?>
    </div>

    <!-- RECENT LOGS -->
    <div>
        <h4 style="margin: 0 0 16px 0; color: #333;">📝 Log Terbaru</h4>
        <?php if (!empty($data['recent_logs'])): ?>
            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                <thead>
                    <tr style="background: #f8f9fa; border-bottom: 1px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Item</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Tanggal</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Pelaksana</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Status</th>
                        <th style="padding: 12px; text-align: left; font-weight: 600;">Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['recent_logs'] as $log): ?>
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px; color: #333;"><?= escape($log->nama_item); ?></td>
                            <td style="padding: 12px; color: #666;">
                                <?= date('d/m/Y H:i', strtotime($log->tgl_rencana)); ?>
                            </td>
                            <td style="padding: 12px; color: #666;"><?= escape($log->nama_lengkap ?? 'System'); ?></td>
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
                                <span style="display: inline-block; padding: 4px 8px; background: <?= $status['bg']; ?>; color: <?= $status['color']; ?>; border-radius: 4px; font-size: 12px;">
                                    <?= $status['label']; ?>
                                </span>
                            </td>
                            <td style="padding: 12px; color: #666;"><?= escape($log->kondisi_laporan ?? 'Normal'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div style="padding: 40px 20px; text-align: center; color: #999;">
                <i class="bi bi-file-earmark-text" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
                <p style="margin: 0;">Belum ada log maintenance</p>
            </div>
        <?php endif; ?>
    </div>
</div>
