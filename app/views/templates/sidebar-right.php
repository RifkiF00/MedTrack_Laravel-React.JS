<?php
// Dynamic sidebar based on user role
$sidebarData = $data['sidebar_data'] ?? [];
$role = $data['role'] ?? '';
?>

<!-- ========== STAF IPSRS SIDEBAR ========== -->
<?php if ($role === 'Staf_IPSRS'): ?>

    <!-- WORK ORDER BARU -->
    <?php if (!empty($sidebarData['wo_open'])): ?>
    <div style="margin-bottom: 24px;" class="sidebar-group">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">🔴</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Work Order Baru</h4>
        </div>
        <div style="background: #fff5f5; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['wo_open'], 0, 8) as $wo): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #ffecec; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#ffecec'" onmouseout="this.style.background='transparent'" data-search="<?= strtolower(escape($wo->kode_label)); ?>">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($wo->kode_label); ?></div>
                <div style="color: #cd1601; font-size: 11px; font-weight: 600;"><?= $wo->tingkat_urgensi; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- PRIORITY WO -->
    <?php if (!empty($sidebarData['wo_priority'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">⚡</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Prioritas Tinggi</h4>
        </div>
        <div style="background: #fffbf0; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['wo_priority'], 0, 8) as $wo): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #ffecd1; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fffaf5'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($wo->kode_label); ?></div>
                <div style="color: #f59e0b; font-size: 11px; font-weight: 600;"><?= $wo->status_ticket; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- WO IN PROGRESS -->
    <?php if (!empty($sidebarData['wo_progress'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">🔧</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Sedang Dikerjakan</h4>
        </div>
        <div style="background: #f0f4fe; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['wo_progress'], 0, 8) as $wo): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #d0d9f0; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f7ff'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($wo->kode_label); ?></div>
                <div style="color: #8e9bb0; font-size: 11px;">Teknisi: <?= escape($wo->nama_lengkap ?? 'N/A'); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- MAINTENANCE TODAY -->
    <?php if (!empty($sidebarData['maintenance_hari_ini'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">📅</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Maintenance Hari Ini</h4>
        </div>
        <div style="background: #fafbfc; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['maintenance_hari_ini'], 0, 8) as $m): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f7fa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($m->nama_item); ?></div>
                <div style="color: <?= $m->status_pelaksanaan === 'Terselesaikan' ? '#10b981' : '#f59e0b'; ?>; font-size: 11px; font-weight: 600;">
                    <?= $m->status_pelaksanaan; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- DAMAGED ASSETS -->
    <?php if (!empty($sidebarData['aset_rusak'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">⚠️</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Aset Rusak</h4>
        </div>
        <div style="background: #fef2f2; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['aset_rusak'], 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #fdd8d8; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fffafa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600;"><?= escape($aset->kode_label); ?> - <?= escape($aset->nama_alat); ?></div>
                <div style="color: #8e9bb0; font-size: 11px;"><?= escape($aset->status_kondisi ?? 'N/A'); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- CRITICAL ASSETS -->
    <?php if (!empty($sidebarData['aset_kritis'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">🚨</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Aset Kritis</h4>
        </div>
        <div style="background: #f0f4ff; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['aset_kritis'], 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #d9e5ff; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f8ff'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600;"><?= escape($aset->kode_label); ?> - <?= escape($aset->nama_alat); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

<!-- ========== STAF LOGISTIK SIDEBAR ========== -->
<?php elseif ($role === 'Staf_Logistik'): ?>

    <!-- RECENT ASSETS -->
    <?php if (!empty($sidebarData['aset_recent'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">📦</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Aset Baru</h4>
        </div>
        <div style="background: #fafbfc; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['aset_recent'], 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f7fa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($aset->kode_label); ?></div>
                <div style="color: #8e9bb0; font-size: 11px;"><?= escape($aset->nama_alat); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- WAREHOUSE ASSETS -->
    <?php if (!empty($sidebarData['aset_gudang'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">🏢</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Aset di Gudang</h4>
        </div>
        <div style="background: #f0f8f5; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['aset_gudang'], 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #d4f0e8; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5faf8'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600;"><?= escape($aset->kode_label); ?> - <?= escape($aset->nama_alat); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- RECENT TRANSFERS -->
    <?php if (!empty($sidebarData['mutasi_terbaru'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">🔄</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Mutasi Terbaru</h4>
        </div>
        <div style="background: #fafbfc; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['mutasi_terbaru'], 0, 8) as $m): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f7fa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($m->kode_label); ?></div>
                <div style="color: #8e9bb0; font-size: 11px;"><?= escape($m->asal); ?> → <?= escape($m->tujuan); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- PROCUREMENT NEEDED -->
    <?php if (!empty($sidebarData['aset_perlu_pengadaan'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">🛒</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Perlu Pengadaan</h4>
        </div>
        <div style="background: #fef2f2; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['aset_perlu_pengadaan'], 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #fdd8d8; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fffafa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600;"><?= escape($aset->kode_label); ?> - <?= escape($aset->status_kondisi); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- NO CERTIFICATE -->
    <?php if (!empty($sidebarData['aset_tanpa_sertifikat'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">⚠️</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Tanpa Sertifikat</h4>
        </div>
        <div style="background: #fffbf0; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['aset_tanpa_sertifikat'], 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #ffecd1; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fffaf5'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600;"><?= escape($aset->kode_label); ?> - <?= escape($aset->nama_alat); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

<!-- ========== UNIT PENGGUNA SIDEBAR ========== -->
<?php elseif ($role === 'Unit_RS'): ?>

    <!-- WO YANG DIBUAT -->
    <?php if (!empty($sidebarData['wo_buat'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">📋</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">WO Saya</h4>
        </div>
        <div style="background: #fafbfc; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['wo_buat'], 0, 8) as $wo): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f7fa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($wo->kode_label); ?></div>
                <div style="color: <?= $wo->status_ticket === 'Closed' ? '#10b981' : '#f59e0b'; ?>; font-size: 11px; font-weight: 600;">
                    <?= $wo->status_ticket; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- UNRESOLVED WO -->
    <?php if (!empty($sidebarData['wo_unresolved'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">🔔</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Belum Ditangani</h4>
        </div>
        <div style="background: #fef2f2; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['wo_unresolved'], 0, 8) as $wo): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #fdd8d8; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fffafa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($wo->kode_label); ?></div>
                <div style="color: #cd1601; font-size: 11px; font-weight: 600;"><?= $wo->status_ticket; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- ASSETS IN ROOM -->
    <?php if (!empty($sidebarData['aset_ruangan'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">📍</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Aset Ruangan</h4>
        </div>
        <div style="background: #f0f4ff; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['aset_ruangan'], 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #d9e5ff; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f8ff'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600;"><?= escape($aset->kode_label); ?> - <?= escape($aset->nama_alat); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- DAMAGED ASSETS IN ROOM -->
    <?php if (!empty($sidebarData['aset_rusak_ruangan'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">⚠️</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Aset Rusak</h4>
        </div>
        <div style="background: #fef2f2; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['aset_rusak_ruangan'], 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #fdd8d8; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fffafa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($aset->kode_label); ?></div>
                <div style="color: #8e9bb0; font-size: 11px;"><?= escape($aset->status_kondisi); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- WO COMPLETED -->
    <?php if (!empty($sidebarData['wo_completed'])): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">✅</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">WO Selesai</h4>
        </div>
        <div style="background: #f0fef8; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($sidebarData['wo_completed'], 0, 8) as $wo): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #d1f2e8; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5fdf9'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600;"><?= escape($wo->kode_label); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>

<!-- ========== DASHBOARD PAGE - STATUS & REMINDERS ========== -->
<?php if (($data['content_view'] ?? '') === 'dashboard/index'): ?>

    <?php
        $jadwal_minggu_maintenance = $data['jadwal_minggu_maintenance'] ?? [];
        $jadwal_minggu_wo = $data['jadwal_minggu_wo'] ?? [];
        $total_maintenance = 0;
        $total_wo = 0;

        foreach ($jadwal_minggu_maintenance as $j) {
            $total_maintenance += $j->total_jadwal ?? 0;
        }
        foreach ($jadwal_minggu_wo as $j) {
            $total_wo += $j->total_wo ?? 0;
        }
    ?>

    <!-- STATUS MAINTENANCE & KALIBRASI -->
    <div style="margin-bottom: 24px;">
        <div style="display: flex; gap: 12px;">
            <!-- Maintenance Status -->
            <div style="flex: 1; padding: 12px 14px; background: #fef3c7; border-radius: 8px; border-left: 4px solid #f59e0b;">
                <div style="font-size: 11px; color: #8e9bb0; margin-bottom: 4px; font-weight: 600;">📅 Maintenance</div>
                <div style="font-size: 18px; font-weight: 700; color: #1a2b56;"><?= $total_maintenance; ?></div>
                <div style="font-size: 10px; color: #8e9bb0;">minggu ini</div>
            </div>

            <!-- Work Order Status -->
            <div style="flex: 1; padding: 12px 14px; background: #fef2f2; border-radius: 8px; border-left: 4px solid #cd1601;">
                <div style="font-size: 11px; color: #8e9bb0; margin-bottom: 4px; font-weight: 600;">🔴 Work Order</div>
                <div style="font-size: 18px; font-weight: 700; color: #1a2b56;"><?= $total_wo; ?></div>
                <div style="font-size: 10px; color: #8e9bb0;">minggu ini</div>
            </div>
        </div>
    </div>

<?php endif; ?>
<?php if (($data['content_view'] ?? '') === 'aset/index'): ?>

    <?php
        $maintenance_today = $data['jadwal_hari_ini_maintenance'] ?? [];
    ?>

    <!-- TODAY'S SCHEDULE -->
    <?php if (!empty($maintenance_today)): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">📅</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Jadwal Kalibrasi</h4>
        </div>
        <div style="background: #fafbfc; border-radius: 8px; overflow: hidden;">
            <?php foreach ($maintenance_today as $m): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f7fa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($m->nama_item); ?></div>
                <div style="color: #8e9bb0; font-size: 11px; margin-bottom: 2px;">📍 <?= escape($m->lokasi ?? 'N/A'); ?></div>
                <div style="color: <?= $m->status_pelaksanaan === 'Terselesaikan' ? '#10b981' : '#f59e0b'; ?>; font-size: 11px; font-weight: 600;"><?= $m->status_pelaksanaan; ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php else: ?>
    <div style="padding: 12px; text-align: center; color: #8e9bb0; font-size: 12px; background: #f9fafb; border-radius: 8px; margin-bottom: 24px;">
        Tidak ada jadwal kalibrasi untuk hari ini
    </div>
    <?php endif; ?>

<?php endif; ?>


<?php if (($data['content_view'] ?? '') === 'aset/index'): ?>

    <?php
        $aset_rusak_ringan = $data['aset_rusak_ringan'] ?? [];
        $aset_rusak_berat = $data['aset_rusak_berat'] ?? [];
        $aset_maintenance = $data['aset_maintenance'] ?? [];
        $aset_gudang = $data['aset_gudang'] ?? [];
    ?>

    <div style="background: #fff3cd; padding: 12px; margin-bottom: 12px; border-radius: 8px; font-size: 10px; color: #856404; word-break: break-word;">
        <strong>🔍 DEBUG:</strong><br>
        Ringan: <?= count($aset_rusak_ringan) ?> items<br>
        Berat: <?= count($aset_rusak_berat) ?> items<br>
        Maint: <?= count($aset_maintenance) ?> items<br>
        Gudang: <?= count($aset_gudang) ?> items
    </div>

    <!-- RUSAK RINGAN -->
    <?php if (!empty($aset_rusak_ringan)): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">⚠️</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Rusak Ringan</h4>
        </div>
        <div style="background: #fffbf0; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($aset_rusak_ringan, 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #ffecd1; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fffaf5'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($aset->kode_label); ?></div>
                <div style="color: #8e9bb0; font-size: 11px;"><?= escape($aset->nama_alat); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- RUSAK BERAT -->
    <?php if (!empty($aset_rusak_berat)): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">🔴</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Rusak Berat</h4>
        </div>
        <div style="background: #fef2f2; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($aset_rusak_berat, 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #fdd8d8; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#fffafa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($aset->kode_label); ?></div>
                <div style="color: #8e9bb0; font-size: 11px;"><?= escape($aset->nama_alat); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- MAINTENANCE -->
    <?php if (!empty($aset_maintenance)): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">🔧</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Maintenance</h4>
        </div>
        <div style="background: #fafbfc; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($aset_maintenance, 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f7fa'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($aset->kode_label); ?></div>
                <div style="color: #8e9bb0; font-size: 11px;"><?= escape($aset->nama_alat); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- GUDANG -->
    <?php if (!empty($aset_gudang)): ?>
    <div style="margin-bottom: 24px;">
        <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
            <span style="font-size: 14px;">📦</span>
            <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Gudang</h4>
        </div>
        <div style="background: #f0f8f5; border-radius: 8px; overflow: hidden;">
            <?php foreach (array_slice($aset_gudang, 0, 8) as $aset): ?>
            <div style="padding: 10px 12px; border-bottom: 1px solid #d4f0e8; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5faf8'" onmouseout="this.style.background='transparent'">
                <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($aset->kode_label); ?></div>
                <div style="color: #8e9bb0; font-size: 11px;"><?= escape($aset->nama_alat); ?></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

<?php endif; ?>
