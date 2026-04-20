<div class="card" style="padding: 24px;">
    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; background: <?= $data['flash']['type'] === 'success' ? '#ecfdf5' : '#fee'; ?>; color: <?= $data['flash']['type'] === 'success' ? '#047857' : '#c92a2a'; ?>;">
            <?= $data['flash']['type'] === 'success' ? '✓' : '✕'; ?> <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- PROFILE PHOTO UPLOAD SECTION -->
    <div style="text-align: center; margin-bottom: 32px;">
        <!-- Current Profile Photo Display -->
        <div style="width: 120px; height: 120px; margin: 0 auto 16px; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 4px solid #3d6aff; background: #f0f4f8;">
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
                <label style="position: relative; cursor: pointer; background: #f0f4f8; padding: 8px 12px; border-radius: 8px; border: 2px dashed #3d6aff; transition: 0.3s; font-size: 13px; font-weight: 600; color: #1a2b56;">
                    📷 Pilih Foto
                    <input type="file" name="profile_photo" accept=".jpg,.jpeg,.png,.webp" style="display: none;" onchange="this.form.submit()">
                </label>
                <span style="font-size: 12px; color: #8e9bb0;">Max 5MB (jpg, png, webp)</span>
            </div>
        </form>

        <h2 style="margin: 24px 0 8px 0; font-size: 22px; font-weight: 700; color: #1a2b56;">
            <?= escape($data['user']->nama_lengkap); ?>
        </h2>
        <p style="margin: 0; color: #8e9bb0; font-size: 14px;">
            @<?= escape($data['user']->username); ?>
        </p>
    </div>

    <!-- USER IDENTITY CARDS -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 24px;">

        <!-- Email -->
        <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #3d6aff;">
            <p style="margin: 0; font-size: 12px; color: #8e9bb0; margin-bottom: 4px;">EMAIL</p>
            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #1a2b56;">
                <?= escape($data['user']->email ?? '-'); ?>
            </p>
        </div>

        <!-- No. HP (Phone) -->
        <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #10b981;">
            <p style="margin: 0; font-size: 12px; color: #8e9bb0; margin-bottom: 4px;">TELEPON</p>
            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #1a2b56;">
                <?= escape($data['user']->no_hp ?? '-'); ?>
            </p>
        </div>

        <!-- NIP -->
        <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #cd1601;">
            <p style="margin: 0; font-size: 12px; color: #8e9bb0; margin-bottom: 4px;">NIP</p>
            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #1a2b56;">
                <?= escape($data['user']->nip ?? '-'); ?>
            </p>
        </div>

        <!-- Department/Ruang -->
        <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #7950f2;">
            <p style="margin: 0; font-size: 12px; color: #8e9bb0; margin-bottom: 4px;">DEPARTEMEN</p>
            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #1a2b56;">
                <?= escape($data['user']->nama_ruang ?? '-'); ?>
            </p>
        </div>

        <!-- Role -->
        <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #fab005;">
            <p style="margin: 0; font-size: 12px; color: #8e9bb0; margin-bottom: 4px;">ROLE</p>
            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #1a2b56;">
                <?php
                    $roles = [
                        'Staf_IPSRS' => 'Staf IPSRS',
                        'Staf_Logistik' => 'Staf Logistik',
                        'Unit_RS' => 'Unit Rumah Sakit',
                        'Admin_IPSRS' => 'Admin IPSRS',
                        'Kepala_IPSRS' => 'Kepala IPSRS'
                    ];
                    echo $roles[$data['user']->role] ?? escape($data['user']->role);
                ?>
            </p>
        </div>

        <!-- Last Login -->
        <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #495057;">
            <p style="margin: 0; font-size: 12px; color: #8e9bb0; margin-bottom: 4px;">LOGIN TERAKHIR</p>
            <p style="margin: 0; font-size: 14px; font-weight: 600; color: #1a2b56;">
                <?php
                    if ($data['user']->last_login) {
                        $date = new DateTime($data['user']->last_login);
                        echo $date->format('d M Y, H:i');
                    } else {
                        echo '-';
                    }
                ?>
            </p>
        </div>
    </div>
</div>
