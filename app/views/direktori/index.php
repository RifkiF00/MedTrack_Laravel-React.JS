<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff;">
    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: #ecfdf5; color: #047857; font-size: 14px; font-family: 'Nunito', sans-serif;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- TABS -->
    <div style="margin-bottom: 24px; border-bottom: 1px solid #e5e7eb; display: flex; gap: 24px;">
        <button onclick="switchTab('units')" id="tab-units" style="padding: 12px 0; border: none; background: none; border-bottom: 3px solid #3d6aff; color: #3d6aff; font-weight: 600; cursor: pointer; font-family: 'Nunito', sans-serif; font-size: 14px;">
            📍 Unit & Ruangan
        </button>
        <button onclick="switchTab('sdm')" id="tab-sdm" style="padding: 12px 0; border: none; background: none; border-bottom: 3px solid transparent; color: #8e9bb0; font-weight: 600; cursor: pointer; font-family: 'Nunito', sans-serif; font-size: 14px;">
            👥 SDM & Staf
        </button>
        <button onclick="switchTab('emergency')" id="tab-emergency" style="padding: 12px 0; border: none; background: none; border-bottom: 3px solid transparent; color: #8e9bb0; font-weight: 600; cursor: pointer; font-family: 'Nunito', sans-serif; font-size: 14px;">
            📞 Kontak Darurat
        </button>
    </div>

    <!-- TAB: UNITS & ROOMS -->
    <div id="content-units" style="display: block;">
        <?php if (!empty($data['ruangan_list'])): ?>
            <div style="display: grid; gap: 12px;">
                <?php foreach ($data['ruangan_list'] as $ruangan): ?>
                    <div style="padding: 16px; border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
                        <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                                    <?= escape($ruangan->nama_ruang); ?>
                                </h3>
                                <p style="margin: 0; font-size: 13px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
                                    📍 Lokasi: <?= escape($ruangan->lokasi ?? 'Tidak ada'); ?>
                                </p>
                                <?php if (!empty($ruangan->deskripsi)): ?>
                                    <p style="margin: 6px 0 0; font-size: 13px; color: #6b7280; font-family: 'Nunito', sans-serif;">
                                        <?= escape($ruangan->deskripsi); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 24px; font-weight: 700; color: #3d6aff; font-family: 'Nunito', sans-serif;">
                                    <?= $ruangan->jumlah_aset ?? 0; ?>
                                </div>
                                <p style="margin: 2px 0 0; font-size: 12px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
                                    Aset
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="padding: 48px 16px; text-align: center; color: #8e9bb0; border-radius: 12px; background: #f9fafb;">
                <i class="bi bi-building" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
                <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px; font-family: 'Nunito', sans-serif;">Belum ada data ruangan</div>
                <div style="font-size: 14px; font-family: 'Nunito', sans-serif;">Ruangan akan ditampilkan di sini</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB: SDM & STAFF -->
    <div id="content-sdm" style="display: none;">
        <?php if (!empty($data['staff_list'])): ?>
            <div style="display: grid; gap: 12px;">
                <?php foreach ($data['staff_list'] as $staff): ?>
                    <div style="padding: 16px; border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
                        <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                                    👤 <?= escape($staff->nama_lengkap); ?>
                                </h3>
                                <p style="margin: 0; font-size: 13px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
                                    <?= escape($staff->role ?? 'Staff'); ?> • <?= escape($staff->nama_ruang ?? 'Umum'); ?>
                                </p>
                                <?php if (!empty($staff->username)): ?>
                                    <p style="margin: 4px 0 0; font-size: 12px; color: #6b7280; font-family: 'Nunito', sans-serif;">
                                        ID: <?= escape($staff->username); ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                            <div style="text-align: right;">
                                <span style="display: inline-block; padding: 4px 10px; background: #eff6ff; color: #0369a1; border-radius: 8px; font-size: 12px; font-weight: 500; font-family: 'Nunito', sans-serif;">
                                    <?php
                                    $roleLabel = [
                                        'Staf_IPSRS' => 'IPSRS',
                                        'Staf_Logistik' => 'Logistik',
                                        'Unit_RS' => 'Unit'
                                    ];
                                    echo $roleLabel[$staff->role] ?? 'User';
                                    ?>
                                </span>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="padding: 48px 16px; text-align: center; color: #8e9bb0; border-radius: 12px; background: #f9fafb;">
                <i class="bi bi-people" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
                <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px; font-family: 'Nunito', sans-serif;">Belum ada data SDM</div>
                <div style="font-size: 14px; font-family: 'Nunito', sans-serif;">Data SDM akan ditampilkan di sini</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- TAB: EMERGENCY CONTACT -->
    <div id="content-emergency" style="display: none;">
        <div style="padding: 48px 16px; text-align: center; color: #8e9bb0; border-radius: 12px; background: #f9fafb;">
            <i class="bi bi-telephone" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
            <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px; font-family: 'Nunito', sans-serif;">Kontak Darurat</div>
            <div style="font-size: 14px; font-family: 'Nunito', sans-serif;">Modul kontak darurat akan segera tersedia</div>
        </div>
    </div>
</div>

<script>
function switchTab(tab) {
    // Hide all content
    document.getElementById('content-units').style.display = 'none';
    document.getElementById('content-sdm').style.display = 'none';
    document.getElementById('content-emergency').style.display = 'none';

    // Remove active state from all tabs
    document.getElementById('tab-units').style.borderColor = 'transparent';
    document.getElementById('tab-units').style.color = '#8e9bb0';
    document.getElementById('tab-sdm').style.borderColor = 'transparent';
    document.getElementById('tab-sdm').style.color = '#8e9bb0';
    document.getElementById('tab-emergency').style.borderColor = 'transparent';
    document.getElementById('tab-emergency').style.color = '#8e9bb0';

    // Show selected content and mark tab as active
    if (tab === 'units') {
        document.getElementById('content-units').style.display = 'block';
        document.getElementById('tab-units').style.borderColor = '#3d6aff';
        document.getElementById('tab-units').style.color = '#3d6aff';
    } else if (tab === 'sdm') {
        document.getElementById('content-sdm').style.display = 'block';
        document.getElementById('tab-sdm').style.borderColor = '#3d6aff';
        document.getElementById('tab-sdm').style.color = '#3d6aff';
    } else if (tab === 'emergency') {
        document.getElementById('content-emergency').style.display = 'block';
        document.getElementById('tab-emergency').style.borderColor = '#3d6aff';
        document.getElementById('tab-emergency').style.color = '#3d6aff';
    }
}
</script>
