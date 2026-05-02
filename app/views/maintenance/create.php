<style>
body {
    font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
}
</style>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 0 20px;">

    <!-- HEADER -->
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
        <div>
            <h1 style="margin:0 0 8px 0; font-size:28px; font-weight:700; color:#1a2b56; font-family:'Nunito',sans-serif;">Tambah Jadwal Maintenance</h1>
            <p style="margin:0; color:#8e9bb0; font-size:14px; font-family:'Nunito',sans-serif;">
                Buat jadwal pemeliharaan rutin untuk menjaga kondisi aset optimal.
            </p>
        </div>
        <a href="<?= BASEURL; ?>/maintenance" style="padding:11px 18px; background:#e5e7eb; color:#1a2b56; text-decoration:none; border-radius:10px; font-size:14px; font-weight:600; font-family:'Nunito',sans-serif; transition:all 0.2s;" onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
            ← Kembali
        </a>
    </div>

    <!-- CARD -->
    <div class="card" style="padding:32px; border-radius:16px; background:#ffffff; border:1px solid #e2e8f0; box-shadow:0 1px 3px rgba(0,0,0,0.05);">

        <!-- FLASH MESSAGE -->
        <?php if (!empty($data['flash'])): ?>
            <div style="margin-bottom:20px; padding:14px 16px; border-radius:12px; background:#ecfdf5; color:#047857; font-size:14px; font-family:'Nunito',sans-serif; border-left:4px solid #10b981;">
                ✓ <?= escape($data['flash']['message']); ?>
            </div>
        <?php endif; ?>

        <!-- ERROR MESSAGES -->
        <?php
            $errors = $_SESSION['form_errors'] ?? [];
            if (!empty($errors)):
        ?>
            <div style="margin-bottom:20px; padding:14px 16px; border-radius:12px; background:#fef2f2; border-left:4px solid #dc2626;">
                <?php foreach ($errors as $error): ?>
                    <div style="color:#991b1b; font-size:14px; margin-bottom:6px; font-family:'Nunito',sans-serif;">
                        ✗ <?= escape($error); ?>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php unset($_SESSION['form_errors']); ?>
        <?php endif; ?>

        <!-- FORM -->
        <form method="POST" action="<?= BASEURL; ?>/maintenance/store" style="display:flex; flex-direction:column; gap:20px;">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">

            <!-- GRID LAYOUT -->
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

                <!-- NAMA ITEM / ASSET -->
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#475569; margin-bottom:8px; font-family:'Nunito',sans-serif;">
                        Pilih Aset <span style="color:#dc2626;">*</span>
                    </label>
                    <select name="nama_item" style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; background:#fff; transition:border 0.2s;" onfocus="this.style.borderColor='#3d6aff'" onblur="this.style.borderColor='#cbd5e1'" required>
                        <option value="">-- Pilih Aset --</option>
                        <?php foreach ($data['aset_list'] ?? [] as $aset): ?>
                        <option value="<?= escape($aset->nama_alat); ?>" data-lokasi="<?= escape($aset->nama_ruang ?? ''); ?>">
                            <?= escape($aset->kode_label); ?> - <?= escape($aset->nama_alat); ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- LOKASI (AUTO) -->
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#475569; margin-bottom:8px; font-family:'Nunito',sans-serif;">
                        Lokasi (Auto)
                    </label>
                    <input type="text" id="lokasiField" name="lokasi_item" value="<?= escape($_SESSION['form_old']['lokasi_item'] ?? ''); ?>"
                           style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; background:#f8fafc;"
                           placeholder="Lokasi akan terisi otomatis" readonly>
                </div>

                <!-- FREKUENSI -->
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#475569; margin-bottom:8px; font-family:'Nunito',sans-serif;">
                        Frekuensi Maintenance <span style="color:#dc2626;">*</span>
                    </label>
                    <select name="frekuensi_maintenance" style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; background:#fff; transition:border 0.2s;" onfocus="this.style.borderColor='#3d6aff'" onblur="this.style.borderColor='#cbd5e1'" required>
                        <option value="">-- Pilih Frekuensi --</option>
                        <option value="Harian" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === 'Harian' ? 'selected' : ''; ?>>Harian</option>
                        <option value="2x_Harian" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === '2x_Harian' ? 'selected' : ''; ?>>2x Harian</option>
                        <option value="3x_Harian" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === '3x_Harian' ? 'selected' : ''; ?>>3x Harian</option>
                        <option value="Mingguan" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === 'Mingguan' ? 'selected' : ''; ?>>Mingguan</option>
                        <option value="Bulanan" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === 'Bulanan' ? 'selected' : ''; ?>>Bulanan</option>
                        <option value="3_Bulanan" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === '3_Bulanan' ? 'selected' : ''; ?>>3 Bulanan</option>
                        <option value="6_Bulanan" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === '6_Bulanan' ? 'selected' : ''; ?>>6 Bulanan</option>
                        <option value="Tahunan" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === 'Tahunan' ? 'selected' : ''; ?>>Tahunan</option>
                    </select>
                </div>

                <!-- TANGGAL MAINTENANCE -->
                <div>
                    <label style="display:block; font-size:13px; font-weight:600; color:#475569; margin-bottom:8px; font-family:'Nunito',sans-serif;">
                        Tanggal Maintenance <span style="color:#dc2626;">*</span>
                    </label>
                    <input type="date" name="tanggal_maintenance" value="<?= escape($_SESSION['form_old']['tanggal_maintenance'] ?? ''); ?>"
                           style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; background:#fff; transition:border 0.2s;"
                           onfocus="this.style.borderColor='#3d6aff'" onblur="this.style.borderColor='#cbd5e1'" required>
                </div>
            </div>

            <!-- KETERANGAN FULL WIDTH -->
            <div>
                <label style="display:block; font-size:13px; font-weight:600; color:#475569; margin-bottom:8px; font-family:'Nunito',sans-serif;">
                    Keterangan / Catatan
                </label>
                <textarea name="keterangan" style="width:100%; padding:12px 14px; border:1px solid #cbd5e1; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; min-height:100px; resize:vertical; transition:border 0.2s;"
                          onfocus="this.style.borderColor='#3d6aff'" onblur="this.style.borderColor='#cbd5e1'"
                          placeholder="Jelaskan kegiatan maintenance dan catatan tambahan jika ada..."></textarea>
            </div>

            <!-- BUTTONS -->
            <div style="display:flex; gap:12px; margin-top:12px; border-top:1px solid #e2e8f0; padding-top:20px;">
                <button type="submit" style="flex:1; padding:12px 24px; background:#3d6aff; color:#ffffff; border:none; border-radius:10px; font-size:14px; font-weight:600; font-family:'Nunito',sans-serif; cursor:pointer; transition:all 0.2s; box-shadow:0 2px 4px rgba(61,106,255,0.2);" onmouseover="this.style.background='#2952cc'; this.style.boxShadow='0 4px 8px rgba(61,106,255,0.3)'" onmouseout="this.style.background='#3d6aff'; this.style.boxShadow='0 2px 4px rgba(61,106,255,0.2)'">
                    💾 Simpan Jadwal
                </button>
                <a href="<?= BASEURL; ?>/maintenance" style="flex:1; padding:12px 24px; background:#e5e7eb; color:#1a2b56; text-decoration:none; border-radius:10px; font-size:14px; font-weight:600; font-family:'Nunito',sans-serif; display:flex; align-items:center; justify-content:center; transition:all 0.2s;" onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
                    ← Batal
                </a>
            </div>
        </form>
    </div>
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