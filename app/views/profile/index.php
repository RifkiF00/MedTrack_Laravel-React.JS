<div class="card" style="padding: 24px;">
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; background: #ecfdf5; color: #047857;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <div style="text-align: center; margin-bottom: 32px;">
        <!-- Profile Image -->
        <div style="width: 120px; height: 120px; margin: 0 auto 16px; border-radius: 50%; background: linear-gradient(135deg, #3d6aff, #2952cc); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px; border: 4px solid #3d6aff;">
            👤
        </div>

        <h2 style="margin: 0; font-size: 24px; font-weight: 700; color: #1a2b56;">
            <?= escape($_SESSION['nama_lengkap'] ?? 'User'); ?>
        </h2>
        <p style="margin: 4px 0 0; color: #8e9bb0;">
            ID: <?= escape($_SESSION['username'] ?? '-'); ?>
        </p>
    </div>

    <!-- Info Cards -->
    <div style="display: grid; gap: 16px; margin-bottom: 24px;">
        <div style="padding: 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #3d6aff;">
            <p style="margin: 0 0 6px 0; font-size: 12px; color: #8e9bb0;">ROLE</p>
            <p style="margin: 0; font-size: 16px; font-weight: 600; color: #1a2b56;">
                <?php
                $roleLabel = ['Staf_IPSRS' => 'Staf IPSRS', 'Staf_Logistik' => 'Staf Logistik', 'Unit_RS' => 'Unit Rumah Sakit'];
                echo $roleLabel[$_SESSION['role']] ?? $_SESSION['role'];
                ?>
            </p>
        </div>

        <div style="padding: 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #10b981;">
            <p style="margin: 0 0 6px 0; font-size: 12px; color: #8e9bb0;">STATUS</p>
            <p style="margin: 0; font-size: 16px; font-weight: 600; color: #047857;">✓ Aktif</p>
        </div>
    </div>

    <!-- Upload Section -->
    <div style="margin: 24px 0; padding: 16px; background: #eff6ff; border-radius: 8px; border: 2px dashed #3d6aff; text-align: center;">
        <p style="margin: 0 0 12px 0; font-size: 14px; color: #0369a1; font-weight: 600;">
            📷 Upload Foto Profil
        </p>
        <p style="margin: 0 0 12px 0; font-size: 13px; color: #8e9bb0;">
            Format: JPG, PNG (maks 5MB)
        </p>
        <form method="POST" action="<?= BASEURL; ?>/profile/uploadPhoto" enctype="multipart/form-data" style="display: flex; gap: 8px; justify-content: center;">
            <input type="hidden" name="csrf_token" value="<?= getCSRFToken(); ?>">
            <input type="file" name="profile_photo" accept="image/jpeg,image/png,image/jpg" required>
            <button type="submit" style="padding: 8px 16px; background: #3d6aff; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                Upload
            </button>
        </form>
    </div>

    <!-- Buttons -->
    <div style="display: flex; gap: 12px;">
        <button onclick="alert('Fitur Update Profile akan segera tersedia');"
           style="flex: 1; padding: 12px; background: #3d6aff; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer;">
            ⚙️ Update Profile
        </button>
        <a href="<?= BASEURL; ?>/auth/logout"
           onclick="return confirm('Apakah Anda yakin ingin keluar?')"
           style="flex: 1; padding: 12px; background: #ef4444; color: white; text-align: center; text-decoration: none; border-radius: 8px; font-weight: 600;">
            🚪 Keluar Sistem
        </a>
    </div>
</div>
