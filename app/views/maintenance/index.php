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

    <!-- FILTER -->
    <div style="margin-bottom: 20px; display: flex; gap: 12px; flex-wrap: wrap;">
        <a href="#" style="padding: 10px 16px; background: #0d6efd; color: #fff; text-decoration: none; border-radius: 8px;">
            + Jadwalkan Maintenance
        </a>
        <a href="#" style="padding: 10px 16px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 8px;">
            📋 Lihat Laporan
        </a>
    </div>

    <!-- EMPTY STATE -->
    <div style="padding: 60px 20px; text-align: center; color: #999;">
        <i class="bi bi-tools" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
        <p style="margin: 0; font-size: 16px;">Belum ada jadwal maintenance</p>
        <p style="margin: 8px 0 0; font-size: 13px; color: #bbb;">Buat jadwal pemeliharaan rutin untuk aset-aset penting</p>
    </div>
</div>
