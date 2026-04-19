<div class="card" style="padding: 24px;">
    <div style="margin-bottom: 24px;">
        <h3 style="margin: 0 0 8px 0;">Direktori Unit & SDM</h3>
        <p style="margin: 0; color: #666; font-size: 14px;">
            Daftar unit, ruangan, dan sumber daya manusia di rumah sakit
        </p>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 14px; border-radius: 8px; background: #eaf7ee; color: #1f7a3d;">
            <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- TABS -->
    <div style="margin-bottom: 20px; border-bottom: 1px solid #e5e7eb; display: flex; gap: 20px;">
        <button style="padding: 12px 0; border: none; background: none; border-bottom: 2px solid #0d6efd; color: #0d6efd; font-weight: 600; cursor: pointer;">
            📍 Unit & Ruangan
        </button>
        <button style="padding: 12px 0; border: none; background: none; border-bottom: 2px solid transparent; color: #999; font-weight: 600; cursor: pointer;">
            👥 SDM & Staf
        </button>
        <button style="padding: 12px 0; border: none; background: none; border-bottom: 2px solid transparent; color: #999; font-weight: 600; cursor: pointer;">
            📞 Kontak Darurat
        </button>
    </div>

    <!-- CONTENT -->
    <div style="padding: 40px 20px; text-align: center; color: #999;">
        <i class="bi bi-building" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
        <p style="margin: 0; font-size: 16px;">Daftar unit sedang dimuat...</p>
        <p style="margin: 8px 0 0; font-size: 13px; color: #bbb;">Modul direktori akan menampilkan struktur organisasi rumah sakit</p>
    </div>
</div>
