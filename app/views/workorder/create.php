<?php
$errors = $data['errors'] ?? [];
$old = $data['old'] ?? [];
$asetList = $data['aset'] ?? [];
?>

<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff;">

    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px; gap:15px; flex-wrap:wrap;">
        <div>
            <h1 style="margin:0; font-size:28px; font-weight:700; color:#1a2b56; font-family:'Nunito',sans-serif;">Form Work Order</h1>
            <p style="margin:8px 0 0; color:#8e9bb0; font-family:'Nunito',sans-serif; font-size:14px;">Laporkan kerusakan aset untuk ditindaklanjuti oleh IPSRS.</p>
        </div>

        <div>
            <a href="<?= BASEURL; ?>/workorder"
               style="padding:11px 18px; background:#e5e7eb; color:#1a2b56; text-decoration:none; border-radius:10px; font-size:14px; font-weight:600; font-family:'Nunito',sans-serif; transition:all 0.2s;"
               onmouseover="this.style.background='#d1d5db'"
               onmouseout="this.style.background='#e5e7eb'">
                ✕ Kembali
            </a>
        </div>
    </div>

    <?php if (!empty($errors)): ?>
        <div style="margin-bottom:20px; padding:12px 16px; border-radius:12px; background:#fef2f2; color:#dc2626; font-size:14px; font-family:'Nunito',sans-serif;">
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

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; max-width:800px;">

            <!-- PILIH ASET -->
            <div style="grid-column:1 / -1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#1a2b56; font-size:14px; font-family:'Nunito',sans-serif;">Pilih Aset <span style="color:#cd1601;">*</span></label>
                <select name="id_aset"
                        style="width:100%; padding:11px 12px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; color:#1a2b56; background:#ffffff;"
                        onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                        required>
                    <option value="">-- Pilih Aset --</option>
                    <?php foreach ($asetList as $aset): ?>
                        <option value="<?= $aset->id_aset; ?>"
                            <?= (($old['id_aset'] ?? '') == $aset->id_aset) ? 'selected' : ''; ?>>
                            <?= escape(($aset->kode_label ?? '-') . ' - ' . ($aset->nama_alat ?? '-') . ' (' . ($aset->nama_ruang ?? '-') . ')'); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- TINGKAT URGENSI -->
            <div>
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#1a2b56; font-size:14px; font-family:'Nunito',sans-serif;">Tingkat Urgensi <span style="color:#cd1601;">*</span></label>
                <select name="tingkat_urgensi"
                        style="width:100%; padding:11px 12px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; color:#1a2b56; background:#ffffff;"
                        onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'"
                        onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                        required>
                    <option value="Rendah" <?= (($old['tingkat_urgensi'] ?? '') === 'Rendah') ? 'selected' : ''; ?>>Rendah</option>
                    <option value="Sedang" <?= (($old['tingkat_urgensi'] ?? 'Sedang') === 'Sedang') ? 'selected' : ''; ?>>Sedang</option>
                    <option value="Tinggi" <?= (($old['tingkat_urgensi'] ?? '') === 'Tinggi') ? 'selected' : ''; ?>>Tinggi</option>
                    <option value="Darurat" <?= (($old['tingkat_urgensi'] ?? '') === 'Darurat') ? 'selected' : ''; ?>>Darurat</option>
                </select>
            </div>

            <!-- STATUS AWAL -->
            <div>
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#1a2b56; font-size:14px; font-family:'Nunito',sans-serif;">Status Awal</label>
                <input type="text"
                       value="Open"
                       readonly
                       style="width:100%; padding:11px 12px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; background:#f9fafb; color:#8e9bb0;">
            </div>

            <!-- DESKRIPSI -->
            <div style="grid-column:1 / -1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#1a2b56; font-size:14px; font-family:'Nunito',sans-serif;">Deskripsi Kerusakan <span style="color:#cd1601;">*</span></label>
                <textarea name="deskripsi_kerusakan"
                          rows="6"
                          placeholder="Jelaskan kerusakan atau kendala aset secara singkat dan jelas..."
                          style="width:100%; padding:11px 12px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; color:#1a2b56; background:#ffffff; resize:vertical;"
                          onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'"
                          onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"
                          required><?= escape($old['deskripsi_kerusakan'] ?? ''); ?></textarea>
            </div>

            <!-- FOTO KERUSAKAN -->
            <div style="grid-column:1 / -1;">
                <label style="display:block; margin-bottom:8px; font-weight:600; color:#1a2b56; font-size:14px; font-family:'Nunito',sans-serif;">Foto Kerusakan</label>
                <input type="file"
                       name="foto_kerusakan"
                       accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                       style="width:100%; padding:11px 12px; border:1px solid #e5e7eb; border-radius:10px; font-size:14px; font-family:'Nunito',sans-serif; background:#ffffff;">
                <small style="color:#8e9bb0; font-size:12px; font-family:'Nunito',sans-serif;">Format yang didukung: JPG, JPEG, PNG, WEBP.</small>
            </div>

        </div>

        <div style="margin-top:24px; display:flex; gap:12px; flex-wrap:wrap; max-width:800px;">
            <button type="submit"
                    style="padding:11px 20px; background:#3d6aff; color:#fff; border:none; border-radius:10px; cursor:pointer; font-size:14px; font-weight:600; font-family:'Nunito',sans-serif; transition:all 0.2s;"
                    onmouseover="this.style.background='#2952cc'"
                    onmouseout="this.style.background='#3d6aff'">
                ✓ Simpan Work Order
            </button>

            <a href="<?= BASEURL; ?>/workorder"
               style="padding:11px 20px; background:#e5e7eb; color:#1a2b56; text-decoration:none; border-radius:10px; font-size:14px; font-weight:600; font-family:'Nunito',sans-serif; transition:all 0.2s;"
               onmouseover="this.style.background='#d1d5db'"
               onmouseout="this.style.background='#e5e7eb'">
                ✕ Batal
            </a>
        </div>
    </form>

</div>