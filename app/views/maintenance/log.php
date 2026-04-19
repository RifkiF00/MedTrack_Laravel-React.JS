<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff;">
    <!-- HEADER -->
    <div style="margin-bottom: 24px;">
        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
            Input Log Maintenance
        </h1>
        <p style="margin: 0; font-size: 15px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
            Catat pelaksanaan pemeliharaan rutin yang telah selesai
        </p>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: #ecfdf5; color: #047857; font-size: 14px; font-family: 'Nunito', sans-serif;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST" action="<?= BASEURL; ?>/maintenance/log" style="max-width: 500px;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

        <!-- Item Maintenance -->
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Item Maintenance <span style="color: #cd1601;">*</span>
            </label>
            <select name="id_pemeliharaan" required style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff; transition: all 0.2s;" onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                <option value="">-- Pilih Item --</option>
                <?php foreach ($data['pemeliharaan_items'] as $item): ?>
                    <option value="<?= $item->id_pemeliharaan; ?>">
                        <?= escape($item->nama_item); ?> (<?= str_replace('_', ' ', $item->frekuensi); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Tanggal Rencana -->
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Tanggal Pelaksanaan <span style="color: #cd1601;">*</span>
            </label>
            <input type="date" name="tgl_rencana" value="<?= date('Y-m-d'); ?>" required style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff; transition: all 0.2s;" onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
        </div>

        <!-- Status Pelaksanaan -->
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Status <span style="color: #cd1601;">*</span>
            </label>
            <select name="status_pelaksanaan" required style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff; transition: all 0.2s;" onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                <option value="Terselesaikan">✓ Terselesaikan</option>
                <option value="Terjadwal">⏳ Terjadwal</option>
                <option value="Tertunda">⚠ Tertunda</option>
                <option value="Dibatalkan">✕ Dibatalkan</option>
            </select>
        </div>

        <!-- Hasil Pengecekan -->
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Hasil Pengecekan
            </label>
            <textarea name="hasil_pengecekan" rows="3" placeholder="Catat hasil pengecekan/perbaikan..." style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"></textarea>
        </div>

        <!-- Kondisi Laporan -->
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Kondisi <span style="color: #cd1601;">*</span>
            </label>
            <select name="kondisi_laporan" required style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff; transition: all 0.2s;" onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                <option value="Normal">✓ Normal</option>
                <option value="Perlu Perbaikan">⚠ Perlu Perbaikan</option>
                <option value="Rusak">✕ Rusak</option>
                <option value="Penggantian Part">🔧 Penggantian Part</option>
            </select>
        </div>

        <!-- Catatan Khusus -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Catatan Khusus
            </label>
            <textarea name="catatan_khusus" rows="2" placeholder="Catatan tambahan jika ada..." style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"></textarea>
        </div>

        <!-- BUTTONS -->
        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" style="padding: 11px 20px; background: #3d6aff; color: #ffffff; border: none; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; transition: all 0.2s;" onmouseover="this.style.background='#2952cc'" onmouseout="this.style.background='#3d6aff'">
                ✓ Simpan Log
            </button>
            <a href="<?= BASEURL; ?>/maintenance" style="padding: 11px 20px; background: #e5e7eb; color: #1a2b56; text-decoration: none; border-radius: 10px; display: inline-flex; align-items: center; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; transition: all 0.2s;" onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
                ✕ Batal
            </a>
        </div>
    </form>
</div>
