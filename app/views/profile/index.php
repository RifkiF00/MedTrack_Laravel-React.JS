<div class="card" style="padding: 24px;">
    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 8px; background: #ecfdf5; color: #047857;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- PROFILE INFO -->
    <div style="text-align: center; margin-bottom: 32px;">
        <div style="width: 100px; height: 100px; margin: 0 auto 16px; border-radius: 50%; background: linear-gradient(135deg, #3d6aff, #2952cc); display: flex; align-items: center; justify-content: center; color: white; font-size: 40px; border: 4px solid #3d6aff;">
            👤
        </div>

        <h2 style="margin: 0 0 8px 0; font-size: 22px; font-weight: 700; color: #1a2b56;">
            <?= escape($_SESSION['nama_lengkap'] ?? 'User'); ?>
        </h2>
        <p style="margin: 0; color: #8e9bb0; font-size: 14px;">
            <?= escape($_SESSION['username'] ?? 'username'); ?>
        </p>
    </div>

    <!-- INFO CARDS -->
    <div style="margin-bottom: 24px;">
        <div style="padding: 12px 16px; margin-bottom: 12px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #3d6aff;">
            <p style="margin: 0; font-size: 12px; color: #8e9bb0; margin-bottom: 4px;">ROLE</p>
            <p style="margin: 0; font-size: 15px; font-weight: 600; color: #1a2b56;">
                <?php
                $roles = ['Staf_IPSRS' => 'Staf IPSRS', 'Staf_Logistik' => 'Staf Logistik', 'Unit_RS' => 'Unit Rumah Sakit'];
                echo $roles[$_SESSION['role']] ?? $_SESSION['role'];
                ?>
            </p>
        </div>

        <div style="padding: 12px 16px; background: #f9fafb; border-radius: 8px; border-left: 4px solid #10b981;">
            <p style="margin: 0; font-size: 12px; color: #8e9bb0; margin-bottom: 4px;">STATUS</p>
            <p style="margin: 0; font-size: 15px; font-weight: 600; color: #047857;">✓ Aktif</p>
        </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div style="display: flex; gap: 10px;">
        <button onclick="alert('Fitur Update Profile akan segera tersedia');" style="flex: 1; padding: 12px; background: #3d6aff; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; font-size: 14px;">
            ⚙️ Update Profile
        </button>
        <a href="<?= BASEURL; ?>/auth/logout" onclick="return confirm('Yakin ingin keluar?');" style="flex: 1; padding: 12px; background: #ef4444; color: white; text-align: center; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px;">
            🚪 Keluar
        </a>
    </div>
</div>
