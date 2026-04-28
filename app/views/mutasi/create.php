<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff;">
    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: #ecfdf5; color: #047857; font-size: 14px; font-family: 'Nunito', sans-serif;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- FORM -->
    <form method="POST" action="<?= BASEURL; ?>/mutasi/create" style="max-width: 600px;">
        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

        <!-- Pilih Aset -->
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Pilih Aset <span style="color: #cd1601;">*</span>
            </label>
            <select name="id_aset" id="id_aset" required style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff; transition: all 0.2s;" onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                <option value="">-- Pilih Aset --</option>
                <?php foreach ($data['aset_list'] as $aset): ?>
                    <option value="<?= $aset->id_aset; ?>" data-ruang-id="<?= $aset->id_ruang_saat_ini ?? ''; ?>" data-ruang-nama="<?= escape($aset->nama_ruang ?? ''); ?>">
                        <?= escape($aset->kode_label); ?> - <?= escape($aset->nama_alat); ?> (Ruangan: <?= escape($aset->nama_ruang); ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Ruangan Asal (Auto-filled) -->
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Ruangan Asal <span style="color: #cd1601;">*</span>
            </label>
            <input type="text" id="ruang_asal_display" readonly style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #8e9bb0; background: #f9fafb;">
            <input type="hidden" name="ruang_asal" id="ruang_asal">
        </div>

        <!-- Ruangan Tujuan -->
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Ruangan Tujuan <span style="color: #cd1601;">*</span>
            </label>
            <select name="ruang_tujuan" required style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff; transition: all 0.2s;" onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
                <option value="">-- Pilih Ruangan Tujuan --</option>
                <?php foreach ($data['ruangan_list'] as $ruangan): ?>
                    <option value="<?= $ruangan->id_ruang; ?>">
                        <?= escape($ruangan->nama_ruang); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- Alasan Mutasi -->
        <div style="margin-bottom: 18px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Alasan Mutasi
            </label>
            <input type="text" name="alasan_mutasi" placeholder="Contoh: Perubahan tata letak ruangan, Upgrade peralatan, dll" style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff; transition: all 0.2s;" onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
        </div>

        <!-- Catatan -->
        <div style="margin-bottom: 20px;">
            <label style="display: block; font-weight: 600; margin-bottom: 8px; color: #1a2b56; font-size: 14px; font-family: 'Nunito', sans-serif;">
                Catatan Tambahan
            </label>
            <textarea name="catatan" rows="3" placeholder="Catatan atau keterangan lainnya..." style="width: 100%; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff; transition: all 0.2s; resize: vertical;" onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'" onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'"></textarea>
        </div>

        <!-- BUTTONS -->
        <div style="display: flex; gap: 12px; margin-top: 24px;">
            <button type="submit" style="padding: 11px 20px; background: #3d6aff; color: #ffffff; border: none; border-radius: 10px; cursor: pointer; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; transition: all 0.2s;" onmouseover="this.style.background='#2952cc'" onmouseout="this.style.background='#3d6aff'">
                ✓ Catat Mutasi
            </button>
            <a href="<?= BASEURL; ?>/mutasi" style="padding: 11px 20px; background: #e5e7eb; color: #1a2b56; text-decoration: none; border-radius: 10px; display: inline-flex; align-items: center; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; transition: all 0.2s;" onmouseover="this.style.background='#d1d5db'" onmouseout="this.style.background='#e5e7eb'">
                ✕ Batal
            </a>
        </div>
    </form>
</div>

<script>
document.getElementById('id_aset').addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    const ruangId = selectedOption.getAttribute('data-ruang-id');
    const ruangNama = selectedOption.getAttribute('data-ruang-nama');

    if (ruangId) {
        document.getElementById('ruang_asal').value = ruangId;
        document.getElementById('ruang_asal_display').value = ruangNama;
    } else {
        document.getElementById('ruang_asal').value = '';
        document.getElementById('ruang_asal_display').value = '';
    }
});
</script>
