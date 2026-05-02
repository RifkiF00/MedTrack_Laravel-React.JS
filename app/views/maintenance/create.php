<div class="container" style="max-width:900px; margin:30px auto; padding:0 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:15px; flex-wrap:wrap;">
        <div>
            <h2 style="margin:0; font-size:24px; font-weight:700; color:#1a2b56;">Tambah Jadwal Maintenance</h2>
            <p style="margin:8px 0 0; color:#666; font-size:14px;">Buat jadwal pemeliharaan rutin untuk aset.</p>
        </div>
        <a href="<?= BASEURL; ?>/maintenance" style="padding:10px 16px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px; font-weight:600;">
            ← Kembali
        </a>
    </div>

    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom:20px; padding:14px 16px; border-radius:8px; background:#d4edda; color:#155724; font-size:14px; border:1px solid #c3e6cb;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <?php
        $errors = $_SESSION['form_errors'] ?? [];
        if (!empty($errors)):
    ?>
        <div style="margin-bottom:20px; padding:14px 16px; border-radius:8px; background:#f8d7da; color:#721c24; font-size:14px; border:1px solid #f5c6cb;">
            <strong>Error:</strong>
            <ul style="margin:10px 0 0 20px;">
                <?php foreach ($errors as $error): ?>
                    <li><?= escape($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['form_errors']); ?>
    <?php endif; ?>

    <form method="POST" action="<?= BASEURL; ?>/maintenance/store" style="background:#fff; border:1px solid #ddd; border-radius:12px; padding:24px;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? ''; ?>">

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

            <div>
                <label style="display:block; font-weight:600; color:#333; font-size:13px; margin-bottom:6px;">Pilih Aset <span style="color:#dc2626;">*</span></label>
                <select name="nama_item" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px;" required>
                    <option value="">-- Pilih Aset --</option>
                    <?php foreach ($data['aset_list'] ?? [] as $aset): ?>
                    <option value="<?= escape($aset->nama_alat); ?>" data-lokasi="<?= escape($aset->nama_ruang ?? ''); ?>">
                        <?= escape($aset->kode_label); ?> - <?= escape($aset->nama_alat); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label style="display:block; font-weight:600; color:#333; font-size:13px; margin-bottom:6px;">Lokasi (Auto)</label>
                <input type="text" id="lokasiField" name="lokasi_item" value="<?= escape($_SESSION['form_old']['lokasi_item'] ?? ''); ?>"
                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px; background:#f9f9f9;" readonly>
            </div>

            <div>
                <label style="display:block; font-weight:600; color:#333; font-size:13px; margin-bottom:6px;">Frekuensi <span style="color:#dc2626;">*</span></label>
                <select name="frekuensi_maintenance" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px;" required>
                    <option value="">-- Pilih Frekuensi --</option>
                    <option value="Harian" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === 'Harian' ? 'selected' : ''; ?>>Harian</option>
                    <option value="2x_Harian" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === '2x_Harian' ? 'selected' : ''; ?>>2x Harian</option>
                    <option value="Mingguan" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === 'Mingguan' ? 'selected' : ''; ?>>Mingguan</option>
                    <option value="Bulanan" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === 'Bulanan' ? 'selected' : ''; ?>>Bulanan</option>
                    <option value="3_Bulanan" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === '3_Bulanan' ? 'selected' : ''; ?>>3 Bulanan</option>
                    <option value="6_Bulanan" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === '6_Bulanan' ? 'selected' : ''; ?>>6 Bulanan</option>
                    <option value="Tahunan" <?= ($_SESSION['form_old']['frekuensi_maintenance'] ?? '') === 'Tahunan' ? 'selected' : ''; ?>>Tahunan</option>
                </select>
            </div>

            <div>
                <label style="display:block; font-weight:600; color:#333; font-size:13px; margin-bottom:6px;">Tanggal Maintenance <span style="color:#dc2626;">*</span></label>
                <input type="date" name="tanggal_maintenance" value="<?= escape($_SESSION['form_old']['tanggal_maintenance'] ?? ''); ?>"
                       style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px;" required>
            </div>

            <div style="grid-column:1 / -1;">
                <label style="display:block; font-weight:600; color:#333; font-size:13px; margin-bottom:6px;">Keterangan</label>
                <textarea name="keterangan" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:14px; min-height:80px; resize:vertical;"
                          placeholder="Jelaskan kegiatan maintenance..."></textarea>
            </div>
        </div>

        <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #f1f5f9; padding-top:20px;">
            <a href="<?= BASEURL; ?>/maintenance" style="padding:10px 16px; color:#64748b; text-decoration:none; font-weight:600; font-size:14px;">Batal</a>
            <button type="submit" style="padding:10px 25px; background:#0d6efd; color:#fff; border:none; border-radius:8px; font-weight:600; font-size:14px; cursor:pointer;">
                Simpan Jadwal
            </button>
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
