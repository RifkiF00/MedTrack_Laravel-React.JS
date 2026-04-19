<?php
$errors = $data['errors'] ?? [];
$old = $data['old'] ?? [];
$asetList = $data['aset'] ?? [];
?>

<div class="card">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:15px; flex-wrap:wrap;">
        <div>
            <h3 style="margin:0;">Form Work Order</h3>
            <p style="margin:8px 0 0; color:#666;">Laporkan kerusakan aset untuk ditindaklanjuti oleh IPSRS.</p>
        </div>

        <div>
            <a href="<?= BASEURL; ?>/workorder"
               style="padding:10px 16px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px;">
                Kembali
            </a>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div style="margin-bottom:20px; padding:14px; border-radius:8px; background:#fee2e2; color:#991b1b;">
            <strong>Terjadi kesalahan:</strong>
            <ul style="margin:10px 0 0 18px; padding:0;">
                <?php foreach ($errors as $error): ?>
                    <li><?= escape($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= BASEURL; ?>/workorder/store" method="POST" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= getCSRFToken(); ?>">

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">

            <!-- PILIH ASET -->
            <div style="grid-column:1 / -1;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Pilih Aset</label>
                <select name="id_aset"
                        style="width:100%; padding:12px; border:1px solid #ccc; border-radius:8px;">
                    <option value="">-- Pilih Aset --</option>
                    <?php foreach ($asetList as $aset): ?>
                        <option value="<?= $aset['id_aset']; ?>"
                            <?= (($old['id_aset'] ?? '') == $aset['id_aset']) ? 'selected' : ''; ?>>
                            <?= escape(($aset['kode_label'] ?? '-') . ' - ' . ($aset['nama_alat'] ?? '-') . ' (' . ($aset['nama_ruang'] ?? '-') . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- TINGKAT URGENSI -->
            <div>
                <label style="display:block; margin-bottom:8px; font-weight:600;">Tingkat Urgensi</label>
                <select name="tingkat_urgensi"
                        style="width:100%; padding:12px; border:1px solid #ccc; border-radius:8px;">
                    <option value="Rendah" <?= (($old['tingkat_urgensi'] ?? '') === 'Rendah') ? 'selected' : ''; ?>>Rendah</option>
                    <option value="Sedang" <?= (($old['tingkat_urgensi'] ?? 'Sedang') === 'Sedang') ? 'selected' : ''; ?>>Sedang</option>
                    <option value="Tinggi" <?= (($old['tingkat_urgensi'] ?? '') === 'Tinggi') ? 'selected' : ''; ?>>Tinggi</option>
                    <option value="Darurat" <?= (($old['tingkat_urgensi'] ?? '') === 'Darurat') ? 'selected' : ''; ?>>Darurat</option>
                </select>
            </div>

            <!-- STATUS AWAL -->
            <div>
                <label style="display:block; margin-bottom:8px; font-weight:600;">Status Awal</label>
                <input type="text"
                       value="Open"
                       readonly
                       style="width:100%; padding:12px; border:1px solid #ddd; border-radius:8px; background:#f8f9fa; color:#666;">
            </div>

            <!-- DESKRIPSI -->
            <div style="grid-column:1 / -1;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Deskripsi Kerusakan</label>
                <textarea name="deskripsi_kerusakan"
                          rows="6"
                          placeholder="Jelaskan kerusakan atau kendala aset secara singkat dan jelas..."
                          style="width:100%; padding:12px; border:1px solid #ccc; border-radius:8px; resize:vertical;"><?= escape($old['deskripsi_kerusakan'] ?? ''); ?></textarea>
            </div>

            <!-- FOTO KERUSAKAN -->
            <div style="grid-column:1 / -1;">
                <label style="display:block; margin-bottom:8px; font-weight:600;">Foto Kerusakan</label>
                <input type="file"
                       name="foto_kerusakan"
                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                       style="width:100%; padding:12px; border:1px solid #ccc; border-radius:8px; background:#fff;">
                <small style="color:#666;">Format yang didukung: JPG, JPEG, PNG, WEBP.</small>
            </div>

        </div>

        <div style="margin-top:24px; display:flex; gap:10px; flex-wrap:wrap;">
            <button type="submit"
                    style="padding:12px 18px; background:#0d6efd; color:#fff; border:none; border-radius:8px; cursor:pointer;">
                Simpan Work Order
            </button>

            <a href="<?= BASEURL; ?>/workorder"
               style="padding:12px 18px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px;">
                Batal
            </a>
        </div>
    </form>

</div>