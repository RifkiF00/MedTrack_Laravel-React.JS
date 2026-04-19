<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff; max-width: 600px;">
    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: #ecfdf5; color: #047857; font-size: 14px; font-family: 'Nunito', sans-serif;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- PROFILE SECTION -->
    <div style="text-align: center; margin-bottom: 32px;">
        <!-- PROFILE IMAGE: Letakkan file di /public/uploads/profiles/{id_user}.png -->
        <img src="<?= BASEURL; ?>/uploads/profiles/<?= $_SESSION['id_user'] ?? '0'; ?>.png"
             onerror="this.src='<?= BASEURL; ?>/uploads/assets/default-avatar.png'"
             style="width: 120px; height: 120px; border-radius: 50%; object-fit: cover; border: 4px solid #3d6aff; margin-bottom: 16px;"
             title="<?= escape($_SESSION['nama_lengkap'] ?? 'User'); ?>">
        <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
            <?= escape($_SESSION['nama_lengkap'] ?? 'User'); ?>
        </h2>
        <p style="margin: 4px 0 0; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
            ID: <?= escape($_SESSION['username'] ?? '-'); ?>
        </p>
    </div>

    <!-- INFO CARDS -->
    <div style="display: grid; gap: 16px;">
        <div style="padding: 16px; background: #f9fafb; border-radius: 12px; border-left: 4px solid #3d6aff;">
            <p style="margin: 0 0 6px 0; font-size: 12px; color: #8e9bb0; font-family: 'Nunito', sans-serif; font-weight: 500;">ROLE</p>
            <p style="margin: 0; font-size: 16px; font-weight: 600; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                <?php
                $roleLabel = [
                    'Staf_IPSRS' => 'Staf IPSRS',
                    'Staf_Logistik' => 'Staf Logistik',
                    'Unit_RS' => 'Unit Rumah Sakit'
                ];
                echo $roleLabel[$_SESSION['role']] ?? $_SESSION['role'];
                ?>
            </p>
        </div>

        <div style="padding: 16px; background: #f9fafb; border-radius: 12px; border-left: 4px solid #10b981;">
            <p style="margin: 0 0 6px 0; font-size: 12px; color: #8e9bb0; font-family: 'Nunito', sans-serif; font-weight: 500;">STATUS</p>
            <p style="margin: 0; font-size: 16px; font-weight: 600; color: #047857; font-family: 'Nunito', sans-serif;">✓ Aktif</p>
        </div>

        <div style="padding: 16px; background: #f9fafb; border-radius: 12px; border-left: 4px solid #0ea5e9;">
            <p style="margin: 0 0 6px 0; font-size: 12px; color: #8e9bb0; font-family: 'Nunito', sans-serif; font-weight: 500;">LOGIN TERAKHIR</p>
            <p style="margin: 0; font-size: 16px; font-weight: 600; color: #1a2b56; font-family: 'Nunito', sans-serif;">Baru saja</p>
        </div>
    </div>

    <!-- INFO BOX -->
    <div style="margin-top: 24px; padding: 14px; background: #eff6ff; border-radius: 10px; border-left: 4px solid #3d6aff;">
        <p style="margin: 0; font-size: 13px; color: #0369a1; font-family: 'Nunito', sans-serif;">
            <strong>📝 Petunjuk Upload Gambar Profil:</strong><br>
            Letakkan file gambar profil Anda di folder:<br>
            <code style="background: #ffffff; padding: 4px 8px; border-radius: 6px; font-size: 12px;">public/uploads/profiles/<?= $_SESSION['id_user'] ?? '{id_user}'; ?>.png</code><br>
            Format: PNG, JPG, atau JPEG (maks 5MB)
        </p>
    </div>

    <!-- LOGOUT BUTTON -->
    <div style="margin-top: 24px; display: flex; gap: 12px;">
        <a href="<?= BASEURL; ?>/auth/logout"
           onclick="return confirm('Apakah Anda yakin ingin keluar?')"
           style="flex: 1; padding: 12px; background: #ef4444; color: #fff; text-align: center; text-decoration: none; border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; transition: all 0.2s;"
           onmouseover="this.style.background='#dc2626'"
           onmouseout="this.style.background='#ef4444'">
            Keluar Sistem
        </a>
    </div>
</div>
