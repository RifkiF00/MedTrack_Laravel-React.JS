<div style="background: #ffffff; border-radius: 20px; padding: 24px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.03);">

    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; background: <?= $data['flash']['type'] === 'success' ? '#ecfdf5' : '#fdecec'; ?>; color: <?= $data['flash']['type'] === 'success' ? '#047857' : '#b42318'; ?>; border-left: 4px solid <?= $data['flash']['type'] === 'success' ? '#10b981' : '#f03e3e'; ?>;">
            <?= $data['flash']['type'] === 'success' ? '✓' : '✕'; ?> <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- VALIDATION ERRORS -->
    <?php if (!empty($data['errors'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; background: #fdecec; color: #b42318; border-left: 4px solid #f03e3e;">
            <strong style="display: block; margin-bottom: 8px;">Periksa input berikut:</strong>
            <ul style="margin: 0; padding-left: 20px;">
                <?php foreach ($data['errors'] as $error): ?>
                    <li style="margin: 4px 0; font-size: 13px;"><?= escape($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <!-- PROFILE PHOTO SECTION -->
    <div style="text-align: center; margin-bottom: 32px;">
        <!-- Current Profile Photo Display -->
        <div style="width: 120px; height: 120px; margin: 0 auto 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 4px solid #3d6aff; background: #f4f7fe;">
            <?php
                $profile_photo = null;
                $allowed_ext = ['jpg', 'jpeg', 'png', 'webp'];
                foreach ($allowed_ext as $ext) {
                    $photo_path = BASEURL . '/uploads/profiles/profile_' . $data['user']->id_user . '.' . $ext;
                    $file_path = '../public/uploads/profiles/profile_' . $data['user']->id_user . '.' . $ext;
                    if (file_exists($file_path)) {
                        $profile_photo = $photo_path;
                        break;
                    }
                }
            ?>
            <?php if ($profile_photo): ?>
                <img src="<?= $profile_photo; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;">
            <?php else: ?>
                <div style="font-size: 60px;">👤</div>
            <?php endif; ?>
        </div>

        <!-- Profile Photo Upload Form -->
        <form method="POST" enctype="multipart/form-data" style="margin-top: 16px;">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken(); ?>">

            <div style="display: flex; gap: 10px; justify-content: center; align-items: center; flex-wrap: wrap;">
                <label style="position: relative; cursor: pointer; background: #f4f7fe; padding: 10px 16px; border-radius: 8px; border: 2px solid #3d6aff; transition: 0.3s; font-size: 13px; font-weight: 600; color: #1a2b56; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
                    📷 Ubah Foto
                    <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp" style="display: none;" onchange="this.form.submit()">
                </label>
                <span style="font-size: 12px; color: #8e9bb0; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">Max 5MB</span>
            </div>
        </form>

        <h2 style="margin: 24px 0 8px 0; font-size: 22px; font-weight: 700; color: #1a2b56; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
            <?= escape($data['user']->nama_lengkap); ?>
        </h2>
        <p style="margin: 0; color: #8e9bb0; font-size: 14px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
            @<?= escape($data['user']->username); ?>
        </p>
    </div>

    <!-- DISPLAY MODE -->
    <?php if (empty($data['edit_mode'])): ?>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
            <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #3d6aff;">
                <p style="margin: 0; font-size: 11px; color: #8e9bb0; margin-bottom: 4px; font-weight: 600; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">EMAIL</p>
                <p style="margin: 0; font-size: 14px; font-weight: 500; color: #1a2b56; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
                    <?= escape($data['user']->email ?? '-'); ?>
                </p>
            </div>

            <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #10b981;">
                <p style="margin: 0; font-size: 11px; color: #8e9bb0; margin-bottom: 4px; font-weight: 600; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">TELEPON</p>
                <p style="margin: 0; font-size: 14px; font-weight: 500; color: #1a2b56; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
                    <?= escape($data['user']->no_hp ?? '-'); ?>
                </p>
            </div>

            <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #cd1601;">
                <p style="margin: 0; font-size: 11px; color: #8e9bb0; margin-bottom: 4px; font-weight: 600; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">NIP</p>
                <p style="margin: 0; font-size: 14px; font-weight: 500; color: #1a2b56; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
                    <?= escape($data['user']->nip ?? '-'); ?>
                </p>
            </div>

            <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #7950f2;">
                <p style="margin: 0; font-size: 11px; color: #8e9bb0; margin-bottom: 4px; font-weight: 600; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">DEPARTEMEN</p>
                <p style="margin: 0; font-size: 14px; font-weight: 500; color: #1a2b56; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
                    <?= escape($data['user']->nama_ruang ?? '-'); ?>
                </p>
            </div>

            <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #fab005;">
                <p style="margin: 0; font-size: 11px; color: #8e9bb0; margin-bottom: 4px; font-weight: 600; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">ROLE</p>
                <p style="margin: 0; font-size: 14px; font-weight: 500; color: #1a2b56; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
                    <?php
                        $roles = ['Staf_IPSRS' => 'Staf IPSRS', 'Staf_Logistik' => 'Staf Logistik', 'Unit_RS' => 'Unit Rumah Sakit', 'Admin_IPSRS' => 'Admin IPSRS', 'Kepala_IPSRS' => 'Kepala IPSRS'];
                        echo $roles[$data['user']->role] ?? escape($data['user']->role);
                    ?>
                </p>
            </div>

            <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #495057;">
                <p style="margin: 0; font-size: 11px; color: #8e9bb0; margin-bottom: 4px; font-weight: 600; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">LOGIN TERAKHIR</p>
                <p style="margin: 0; font-size: 14px; font-weight: 500; color: #1a2b56; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">
                    <?= $data['user']->last_login ? (new DateTime($data['user']->last_login))->format('d M Y, H:i') : '-'; ?>
                </p>
            </div>
        </div>

        <!-- Edit Button -->
        <a href="<?= BASEURL; ?>/profile/edit" style="display: block; width: 100%; padding: 12px; background: #3d6aff; color: white; text-align: center; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; border: none; transition: 0.3s; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;" onmouseover="this.style.background='#2952cc'" onmouseout="this.style.background='#3d6aff'">
            ✏️ Edit Profil
        </a>

    <!-- EDIT MODE -->
    <?php else: ?>

        <form method="POST" action="<?= BASEURL; ?>/profile/update" style="display: flex; flex-direction: column; gap: 16px;">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken(); ?>">

            <!-- Email -->
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: #8e9bb0; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">EMAIL</label>
                <input type="email" name="email" value="<?= escape($data['old']['email'] ?? $data['user']->email); ?>" style="width: 100%; padding: 10px 12px; border: 1px solid #d0d9f0; border-radius: 8px; font-size: 14px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; box-sizing: border-box; transition: 0.3s;" onfocus="this.style.borderColor='#3d6aff'" onblur="this.style.borderColor='#d0d9f0'">
            </div>

            <!-- Phone -->
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: #8e9bb0; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">TELEPON</label>
                <input type="text" name="no_hp" value="<?= escape($data['old']['no_hp'] ?? $data['user']->no_hp); ?>" placeholder="08xxxxxxxxxx" style="width: 100%; padding: 10px 12px; border: 1px solid #d0d9f0; border-radius: 8px; font-size: 14px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; box-sizing: border-box; transition: 0.3s;" onfocus="this.style.borderColor='#3d6aff'" onblur="this.style.borderColor='#d0d9f0'">
            </div>

            <!-- NIP -->
            <div>
                <label style="display: block; margin-bottom: 6px; font-size: 12px; font-weight: 600; color: #8e9bb0; letter-spacing: 0.5px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;">NIP</label>
                <input type="text" name="nip" value="<?= escape($data['old']['nip'] ?? $data['user']->nip); ?>" style="width: 100%; padding: 10px 12px; border: 1px solid #d0d9f0; border-radius: 8px; font-size: 14px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; box-sizing: border-box; transition: 0.3s;" onfocus="this.style.borderColor='#3d6aff'" onblur="this.style.borderColor='#d0d9f0'">
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; gap: 10px; margin-top: 20px;">
                <button type="submit" onclick="return confirm('Yakin ingin menyimpan perubahan profil?')" style="flex: 1; padding: 12px; background: #3d6aff; color: white; border: none; border-radius: 8px; font-weight: 600; font-size: 14px; cursor: pointer; transition: 0.3s; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;" onmouseover="this.style.background='#2952cc'" onmouseout="this.style.background='#3d6aff'">
                    ✓ Simpan Perubahan
                </button>
                <a href="<?= BASEURL; ?>/profile" style="flex: 1; padding: 12px; background: #e9ecef; color: #1a2b56; text-align: center; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; border: none; transition: 0.3s; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;" onmouseover="this.style.background='#dee2e6'" onmouseout="this.style.background='#e9ecef'">
                    ✕ Batal
                </a>
            </div>
        </form>

    <?php endif; ?>

</div>
