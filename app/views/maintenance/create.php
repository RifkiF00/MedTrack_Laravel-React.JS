<style>
body {
    font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
</style>

<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff; max-width: 600px;">
    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: #ecfdf5; color: #047857; font-size: 14px; font-family: 'Nunito', sans-serif;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- ERROR MESSAGES -->
    <?php
        $errors = $_SESSION['form_errors'] ?? [];
        if (!empty($errors)):
    ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: #fef2f2; border-left: 4px solid #dc2626;">
            <?php foreach ($errors as $error): ?>
                <div style="color: #991b1b; font-size: 14px; margin-bottom: 6px; font-family: 'Nunito', sans-serif;">
                    ✗ <?= escape($error); ?>
                </div>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['form_errors']); ?>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST" action="<?= BASEURL; ?>/maintenance/store" style="display: flex; flex-direction: column; gap: 16px;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">

        <!-- NAMA ITEM / ASSET -->
        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; color: #1a2b56; margin-bottom: 6px; font-family: 'Nunito', sans-serif;">
                Pilih Aset <span style="color: #dc2626;">*</span>
            </label>
            <select name="nama_item" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: 'Nunito', sans-serif;" required>
                <option value="">-- Pilih Aset --</option>
                <?php foreach ($data['aset_list'] ?? [] as $aset): ?>
                <option value="<?= escape($aset->nama_alat); ?>" data-lokasi="<?= escape($aset->nama_ruang ?? ''); ?>">
                    <?= escape($aset->kode_label); ?> - <?= escape($aset->nama_alat); ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- LOKASI -->
        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; color: #1a2b56; margin-bottom: 6px; font-family: 'Nunito', sans-serif;">
                Lokasi (Auto)
            </label>
            <input type="text" id="lokasiField" name="lokasi" value="<?= escape($_SESSION['form_old']['lokasi'] ?? ''); ?>"
                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: 'Nunito', sans-serif; background: #f9fafb;"
                   placeholder="Lokasi akan terisi otomatis" readonly>
        </div>

        <!-- FREKUENSI -->
        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; color: #1a2b56; margin-bottom: 6px; font-family: 'Nunito', sans-serif;">
                Frekuensi <span style="color: #dc2626;">*</span>
            </label>
            <select name="frekuensi" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: 'Nunito', sans-serif;" required>
                <option value="">-- Pilih Frekuensi --</option>
                <option value="Harian" <?= ($_SESSION['form_old']['frekuensi'] ?? '') === 'Harian' ? 'selected' : ''; ?>>Harian</option>
                <option value="2x_Harian" <?= ($_SESSION['form_old']['frekuensi'] ?? '') === '2x_Harian' ? 'selected' : ''; ?>>2x Harian</option>
                <option value="3x_Harian" <?= ($_SESSION['form_old']['frekuensi'] ?? '') === '3x_Harian' ? 'selected' : ''; ?>>3x Harian</option>
                <option value="Mingguan" <?= ($_SESSION['form_old']['frekuensi'] ?? '') === 'Mingguan' ? 'selected' : ''; ?>>Mingguan</option>
                <option value="Bulanan" <?= ($_SESSION['form_old']['frekuensi'] ?? '') === 'Bulanan' ? 'selected' : ''; ?>>Bulanan</option>
                <option value="3_Bulanan" <?= ($_SESSION['form_old']['frekuensi'] ?? '') === '3_Bulanan' ? 'selected' : ''; ?>>3 Bulanan</option>
                <option value="6_Bulanan" <?= ($_SESSION['form_old']['frekuensi'] ?? '') === '6_Bulanan' ? 'selected' : ''; ?>>6 Bulanan</option>
                <option value="Tahunan" <?= ($_SESSION['form_old']['frekuensi'] ?? '') === 'Tahunan' ? 'selected' : ''; ?>>Tahunan</option>
            </select>
        </div>

        <!-- PIC PENANGGUNG JAWAB -->
        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; color: #1a2b56; margin-bottom: 6px; font-family: 'Nunito', sans-serif;">
                PIC Penanggung Jawab
            </label>
            <input type="text" name="pic_penanggung_jawab" value="<?= escape($_SESSION['form_old']['pic_penanggung_jawab'] ?? ''); ?>"
                   style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: 'Nunito', sans-serif;"
                   placeholder="nama orang yang bertanggung jawab">
        </div>

        <!-- DESKRIPSI -->
        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; color: #1a2b56; margin-bottom: 6px; font-family: 'Nunito', sans-serif;">
                Deskripsi
            </label>
            <textarea name="deskripsi" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: 'Nunito', sans-serif; min-height: 80px; resize: vertical;"
                      placeholder="Jelaskan kegiatan maintenance"></textarea>
        </div>

        <!-- CATATAN -->
        <div>
            <label style="display: block; font-size: 13px; font-weight: 600; color: #1a2b56; margin-bottom: 6px; font-family: 'Nunito', sans-serif;">
                Catatan
            </label>
            <textarea name="catatan" style="width: 100%; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; font-family: 'Nunito', sans-serif; min-height: 60px; resize: vertical;"
                      placeholder="Catatan tambahan jika ada"></textarea>
        </div>

        <!-- BUTTONS -->
        <div style="display: flex; gap: 12px; margin-top: 12px;">
            <button type="submit" style="flex: 1; padding: 12px 18px; background: #3d6aff; color: #ffffff; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; cursor: pointer; transition: all 0.2s;" onmouseover="this.style.background='#2952cc'" onmouseout="this.style.background='#3d6aff'">
                Simpan Jadwal
            </button>
            <a href="<?= BASEURL; ?>/maintenance" style="flex: 1; padding: 12px 18px; background: #e5e7eb; color: #1a2b56; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; display: flex; align-items: center; justify-content: center; transition: all 0.2s;" onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
                Kembali
            </a>
        </div>
    </form>
</div>

<?php unset($_SESSION['form_old']); ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const namaItemSelect = document.querySelector('select[name="nama_item"]');
    const lokasiField = document.getElementById('lokasiField');

    if (namaItemSelect) {
        namaItemSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const lokasi = selectedOption.getAttribute('data-lokasi') || '';
            lokasiField.value = lokasi;
        });
    }
});
</script>