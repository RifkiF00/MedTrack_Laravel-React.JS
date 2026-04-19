<style>
body {
    font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
</style>

<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff;">
    <!-- HEADER -->
    <div style="margin-bottom: 24px;">
        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
            Mutasi Ruangan
        </h1>
        <p style="margin: 0; font-size: 15px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
            Pencatatan pergerakan aset antar ruangan
        </p>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: #ecfdf5; color: #047857; font-size: 14px; font-family: 'Nunito', sans-serif;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- STATISTICS -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(160px, 1fr)); gap: 12px; margin-bottom: 24px;">
        <div style="padding: 16px; background: #eff6ff; border-radius: 12px; border-left: 4px solid #3d6aff;">
            <div style="font-size: 12px; color: #8e9bb0; margin-bottom: 6px; font-family: 'Nunito', sans-serif; font-weight: 500;">Total Mutasi</div>
            <div style="font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                <?= $data['statistik']->total ?? 0; ?>
            </div>
        </div>

        <div style="padding: 16px; background: #fef3c7; border-radius: 12px; border-left: 4px solid #f59e0b;">
            <div style="font-size: 12px; color: #8e9bb0; margin-bottom: 6px; font-family: 'Nunito', sans-serif; font-weight: 500;">Menunggu Verifikasi</div>
            <div style="font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                <?= $data['statistik']->menunggu ?? 0; ?>
            </div>
        </div>

        <div style="padding: 16px; background: #dbeafe; border-radius: 12px; border-left: 4px solid #0ea5e9;">
            <div style="font-size: 12px; color: #8e9bb0; margin-bottom: 6px; font-family: 'Nunito', sans-serif; font-weight: 500;">Disetujui</div>
            <div style="font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                <?= $data['statistik']->disetujui ?? 0; ?>
            </div>
        </div>

        <div style="padding: 16px; background: #ecfdf5; border-radius: 12px; border-left: 4px solid #10b981;">
            <div style="font-size: 12px; color: #8e9bb0; margin-bottom: 6px; font-family: 'Nunito', sans-serif; font-weight: 500;">Selesai Hari Ini</div>
            <div style="font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                <?= $data['statistik']->selesai_hari_ini ?? 0; ?>
            </div>
        </div>
    </div>

    <!-- ACTION BUTTON -->
    <div style="margin-bottom: 24px;">
        <a href="<?= BASEURL; ?>/mutasi/create" style="padding: 11px 18px; background: #3d6aff; color: #ffffff; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; transition: all 0.2s; display: inline-block;" onmouseover="this.style.background='#2952cc'" onmouseout="this.style.background='#3d6aff'">
            + Catat Mutasi Baru
        </a>
    </div>

    <!-- LIST MUTASI -->
    <div>
        <h2 style="margin: 0 0 16px 0; font-size: 16px; font-weight: 600; color: #1a2b56; font-family: 'Nunito', sans-serif;">
            📋 Daftar Mutasi Terbaru
        </h2>

        <?php if (!empty($data['mutasi_list'])): ?>
            <div style="border-radius: 12px; overflow: hidden; border: 1px solid #e5e7eb;">
                <?php foreach ($data['mutasi_list'] as $i => $mutasi): ?>
                    <div style="padding: 14px 16px; border-bottom: <?= ($i < count($data['mutasi_list']) - 1) ? '1px solid #e5e7eb' : 'none'; ?>;">
                        <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px; margin-bottom: 8px;">
                            <div style="flex: 1;">
                                <div style="font-size: 14px; font-weight: 600; color: #1a2b56; margin-bottom: 4px; font-family: 'Nunito', sans-serif;">
                                    <?= escape($mutasi->kode_label); ?> - <?= escape($mutasi->nama_alat); ?>
                                </div>
                                <div style="font-size: 12px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
                                    <?= escape($mutasi->ruang_asal_nama); ?> → <?= escape($mutasi->ruang_tujuan_nama); ?>
                                </div>
                            </div>

                            <!-- STATUS BADGE -->
                            <?php
                            $statusBg = [
                                'Menunggu_Verifikasi' => '#fef3c7',
                                'Disetujui' => '#dbeafe',
                                'Selesai' => '#ecfdf5',
                                'Ditolak' => '#fef2f2'
                            ];
                            $statusColor = [
                                'Menunggu_Verifikasi' => '#b45309',
                                'Disetujui' => '#0369a1',
                                'Selesai' => '#047857',
                                'Ditolak' => '#991b1b'
                            ];
                            $statusLabel = [
                                'Menunggu_Verifikasi' => '⏳ Menunggu',
                                'Disetujui' => '✓ Disetujui',
                                'Selesai' => '✓ Selesai',
                                'Ditolak' => '✕ Ditolak'
                            ];
                            $status = $mutasi->status_mutasi;
                            ?>
                            <div style="padding: 6px 10px; background: <?= $statusBg[$status] ?? '#f3f4f6'; ?>; color: <?= $statusColor[$status] ?? '#4b5563'; ?>; border-radius: 6px; font-size: 11px; font-weight: 600; font-family: 'Nunito', sans-serif; white-space: nowrap;">
                                <?= $statusLabel[$status] ?? 'Unknown'; ?>
                            </div>
                        </div>

                        <div style="font-size: 12px; color: #8e9bb0; margin-bottom: 10px; font-family: 'Nunito', sans-serif;">
                            <?= date('d/m/Y H:i', strtotime($mutasi->tgl_mutasi)); ?> • <?= escape($mutasi->nama_lengkap ?? 'System'); ?>
                            <?php if ($mutasi->alasan_mutasi): ?>
                                • Alasan: <?= escape($mutasi->alasan_mutasi); ?>
                            <?php endif; ?>
                        </div>

                        <!-- ACTIONS -->
                        <div style="display: flex; gap: 8px; flex-wrap: wrap;">
                            <?php if ($mutasi->status_mutasi === 'Menunggu_Verifikasi'): ?>
                                <a href="<?= BASEURL; ?>/mutasi/approve/<?= $mutasi->id_mutasi; ?>" style="padding: 6px 12px; background: #10b981; color: #fff; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 600; font-family: 'Nunito', sans-serif;">
                                    ✓ Setujui
                                </a>
                                <a href="<?= BASEURL; ?>/mutasi/reject/<?= $mutasi->id_mutasi; ?>" style="padding: 6px 12px; background: #ef4444; color: #fff; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 600; font-family: 'Nunito', sans-serif;">
                                    ✕ Tolak
                                </a>
                            <?php elseif ($mutasi->status_mutasi === 'Disetujui'): ?>
                                <a href="<?= BASEURL; ?>/mutasi/complete/<?= $mutasi->id_mutasi; ?>" style="padding: 6px 12px; background: #3b82f6; color: #fff; text-decoration: none; border-radius: 6px; font-size: 12px; font-weight: 600; font-family: 'Nunito', sans-serif;">
                                    ✓ Selesaikan
                                </a>
                            <?php endif; ?>

                            <form action="<?= BASEURL; ?>/mutasi/delete/<?= $mutasi->id_mutasi; ?>" method="POST" style="display: inline;" onsubmit="return confirm('Hapus mutasi ini?');">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">
                                <button type="submit" style="padding: 6px 12px; background: #6b7280; color: #fff; border: none; border-radius: 6px; font-size: 12px; font-weight: 600; font-family: 'Nunito', sans-serif; cursor: pointer;">
                                    🗑 Hapus
                                </button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="padding: 48px 16px; text-align: center; color: #8e9bb0; border-radius: 12px; background: #f9fafb;">
                <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px; font-family: 'Nunito', sans-serif;">Belum ada data mutasi</div>
                <div style="font-size: 14px; font-family: 'Nunito', sans-serif;">
                    <a href="<?= BASEURL; ?>/mutasi/create" style="color: #3d6aff; text-decoration: none;">
                        Mulai dengan mencatat mutasi ruangan
                    </a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
