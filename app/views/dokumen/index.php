<div class="card" style="padding: 24px;">
    <div style="margin-bottom: 24px;">
        <h3 style="margin: 0 0 8px 0;">Dokumen Mutu</h3>
        <p style="margin: 0; color: #666; font-size: 14px;">
            Arsip dokumen kalibrasi, sertifikat, dan laporan pemeliharaan aset
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
        <input type="text" placeholder="Cari dokumen..."
               style="flex: 1; min-width: 200px; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px;">
        <select style="padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; background: #fff;">
            <option value="">-- Semua Kategori --</option>
            <option value="kalibrasi">Kalibrasi</option>
            <option value="sertifikat">Sertifikat</option>
            <option value="laporan">Laporan</option>
        </select>
        <a href="#" style="padding: 10px 16px; background: #0d6efd; color: #fff; text-decoration: none; border-radius: 6px;">
            + Upload Dokumen
        </a>
    </div>

    <!-- EMPTY STATE -->
    <div style="padding: 60px 20px; text-align: center; color: #999;">
        <i class="bi bi-file-earmark-text" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
        <p style="margin: 0; font-size: 16px;">Belum ada dokumen mutu</p>
        <p style="margin: 8px 0 0; font-size: 13px; color: #bbb;">Upload dokumen kalibrasi dan sertifikat aset</p>
    </div>
</div>
