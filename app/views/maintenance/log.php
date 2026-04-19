<div class="card" style="padding: 24px;">
    <div style="margin-bottom: 24px;">
        <h3 style="margin: 0 0 8px 0;">Input Log Maintenance</h3>
        <p style="margin: 0; color: #666; font-size: 14px;">
            Catat pelaksanaan pemeliharaan rutin yang telah selesai
        </p>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 14px; border-radius: 8px; background: #eaf7ee; color: #1f7a3d;">
            <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST" action="<?= BASEURL; ?>/maintenance/log" style="max-width: 600px;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

        <!-- Item Maintenance -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
                Item Maintenance <span style="color: red;">*</span>
            </label>
            <select name="id_pemeliharaan" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                <option value="">-- Pilih Item --</option>
                <?php foreach ($data['pemeliharaan_items'] as $item): ?>
                    <option value="<?= $item->id_pemeliharaan; ?>">
                        <?= escape($item->nama_item); ?> (<?= str_replace('_', ' ', $item->frekuensi); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Tanggal Rencana -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
                Tanggal Pelaksanaan <span style="color: red;">*</span>
            </label>
            <input type="date" name="tgl_rencana" value="<?= date('Y-m-d'); ?>" required
                   style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
        </div>

        <!-- Status Pelaksanaan -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
                Status <span style="color: red;">*</span>
            </label>
            <select name="status_pelaksanaan" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                <option value="Terselesaikan">✓ Terselesaikan</option>
                <option value="Terjadwal">⏳ Terjadwal</option>
                <option value="Tertunda">⚠ Tertunda</option>
                <option value="Dibatalkan">✕ Dibatalkan</option>
            </select>
        </div>

        <!-- Hasil Pengecekan -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
                Hasil Pengecekan
            </label>
            <textarea name="hasil_pengecekan" rows="4" placeholder="Catat hasil pengecekan/perbaikan..."
                      style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit;"></textarea>
        </div>

        <!-- Kondisi Laporan -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
                Kondisi <span style="color: red;">*</span>
            </label>
            <select name="kondisi_laporan" required style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px;">
                <option value="Normal">✓ Normal</option>
                <option value="Perlu Perbaikan">⚠ Perlu Perbaikan</option>
                <option value="Rusak">✕ Rusak</option>
                <option value="Penggantian Part">🔧 Penggantian Part</option>
            </select>
        </div>

        <!-- Catatan Khusus -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #333;">
                Catatan Khusus
            </label>
            <textarea name="catatan_khusus" rows="3" placeholder="Catatan tambahan jika ada..."
                      style="width: 100%; padding: 10px 12px; border: 1px solid #ddd; border-radius: 6px; font-size: 14px; font-family: inherit;"></textarea>
        </div>

        <!-- BUTTONS -->
        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" style="padding: 10px 24px; background: #0d6efd; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; font-weight: 600;">
                ✓ Simpan Log
            </button>
            <a href="<?= BASEURL; ?>/maintenance" style="padding: 10px 24px; background: #6c757d; color: #fff; text-decoration: none; border-radius: 6px; display: inline-block; font-size: 14px; font-weight: 600;">
                ✕ Batal
            </a>
        </div>
    </form>
</div>
