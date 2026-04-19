<div class="card" style="padding: 24px;">
    <div style="margin-bottom: 24px;">
        <h3 style="margin: 0 0 8px 0;">Daftar Mutasi Ruangan</h3>
        <p style="margin: 0; color: #666; font-size: 14px;">
            Pencatatan pergerakan aset dari satu ruangan ke ruangan lain
        </p>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 14px; border-radius: 8px; background: #eaf7ee; color: #1f7a3d;">
            <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- BUTTON ADD -->
    <div style="margin-bottom: 20px;">
        <a href="#" style="padding: 10px 16px; background: #0d6efd; color: #fff; text-decoration: none; border-radius: 8px;">
            + Tambah Mutasi
        </a>
    </div>

    <!-- EMPTY STATE -->
    <div style="padding: 60px 20px; text-align: center; color: #999;">
        <i class="bi bi-inbox" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
        <p style="margin: 0; font-size: 16px;">Belum ada data mutasi ruangan</p>
        <p style="margin: 8px 0 0; font-size: 13px; color: #bbb;">Mulai dengan menambahkan mutasi aset antar ruangan</p>
    </div>
</div>
