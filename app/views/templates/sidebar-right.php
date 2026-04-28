<?php
// File: app/views/templates/sidebar-right.php

$sidebarData = $data['sidebar_data'] ?? [];
$role = $_SESSION['role'] ?? ($data['role'] ?? '');
$contentView = $data['content_view'] ?? '';

function renderSidebarItem($title, $subtitle = '', $bgHover = '#f5f7fa') {
    ?>
    <div style="padding:10px 12px; border-bottom:1px solid #e5e7eb; font-size:12px; color:#1a2b56; cursor:pointer; transition:0.2s;"
         onmouseover="this.style.background='<?= $bgHover; ?>'"
         onmouseout="this.style.background='transparent'">
        <div style="font-weight:600; margin-bottom:2px;"><?= escape($title); ?></div>
        <?php if (!empty($subtitle)): ?>
            <div style="color:#8e9bb0; font-size:11px;"><?= escape($subtitle); ?></div>
        <?php endif; ?>
    </div>
    <?php
}

function renderSidebarGroup($icon, $title, $items, $callback, $bg = '#fafbfc') {
    if (empty($items)) return;
    ?>
    <div style="margin-bottom:24px;">
        <div style="display:flex; align-items:center; gap:8px; margin-bottom:12px;">
            <span style="font-size:14px;"><?= $icon; ?></span>
            <h4 style="font-size:13px; font-weight:700; color:#1a2b56; margin:0;"><?= escape($title); ?></h4>
        </div>

        <div style="background:<?= $bg; ?>; border-radius:8px; overflow:hidden;">
            <?php foreach (array_slice($items, 0, 8) as $item): ?>
                <?php $callback($item); ?>
            <?php endforeach; ?>
        </div>
    </div>
    <?php
}
?>

<?php if ($contentView === 'dashboard/index'): ?>

    <?php
    $jadwalMingguMaintenance = $data['jadwal_minggu_maintenance'] ?? [];
    $jadwalHariIniMaintenance = $data['jadwal_hari_ini_maintenance'] ?? [];

    $totalMaintenance = 0;
    foreach ($jadwalMingguMaintenance as $j) {
        $totalMaintenance += $j->total_jadwal ?? 0;
    }
    ?>

    <div style="margin-bottom:24px;">
        <div style="display:flex; gap:12px;">
            <a href="<?= BASEURL; ?>/maintenance" style="flex:1; padding:12px 14px; background:#fef3c7; border-radius:8px; border-left:4px solid #f59e0b; text-decoration:none; cursor:pointer; transition:all 0.2s; display:block;"
               onmouseover="this.style.background='#fef08a'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(245,158,11,0.2)';"
               onmouseout="this.style.background='#fef3c7'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="font-size:11px; color:#8e9bb0; margin-bottom:4px; font-weight:600;">📅 Maintenance</div>
                <div style="font-size:18px; font-weight:700; color:#1a2b56;"><?= $totalMaintenance; ?></div>
                <div style="font-size:10px; color:#8e9bb0;">minggu ini</div>
            </a>

            <a href="<?= BASEURL; ?>/maintenance" style="flex:1; padding:12px 14px; background:#f0f4ff; border-radius:8px; border-left:4px solid #3d6aff; text-decoration:none; cursor:pointer; transition:all 0.2s; display:block;"
               onmouseover="this.style.background='#e0e7ff'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 12px rgba(61,106,255,0.2)';"
               onmouseout="this.style.background='#f0f4ff'; this.style.transform='translateY(0)'; this.style.boxShadow='none';">
                <div style="font-size:11px; color:#8e9bb0; margin-bottom:4px; font-weight:600;">✅ Hari Ini</div>
                <div style="font-size:18px; font-weight:700; color:#1a2b56;"><?= count($jadwalHariIniMaintenance); ?></div>
                <div style="font-size:10px; color:#8e9bb0;">jadwal</div>
            </a>
        </div>
    </div>

<?php return; endif; ?>

<?php if ($contentView === 'aset/index'): ?>

    <?php
    renderSidebarGroup('⚠️', 'Rusak Ringan', $sidebarData['aset_rusak_ringan'] ?? [], function($aset) {
        renderSidebarItem(
            ($aset->kode_label ?? '-') . ' - ' . ($aset->nama_alat ?? '-'),
            'Kondisi: ' . ($aset->status_kondisi ?? '-'),
            '#fffaf5'
        );
    }, '#fffbf0');

    renderSidebarGroup('🔴', 'Rusak Berat', $sidebarData['aset_rusak_berat'] ?? [], function($aset) {
        renderSidebarItem(
            ($aset->kode_label ?? '-') . ' - ' . ($aset->nama_alat ?? '-'),
            'Kondisi: ' . ($aset->status_kondisi ?? '-'),
            '#fffafa'
        );
    }, '#fef2f2');

    renderSidebarGroup('🔧', 'Maintenance', $sidebarData['aset_maintenance'] ?? [], function($aset) {
        renderSidebarItem(
            ($aset->kode_label ?? '-') . ' - ' . ($aset->nama_alat ?? '-'),
            'Kondisi: ' . ($aset->status_kondisi ?? '-')
        );
    });

    renderSidebarGroup('📦', 'Gudang', $sidebarData['aset_gudang'] ?? [], function($aset) {
        renderSidebarItem(
            ($aset->kode_label ?? '-') . ' - ' . ($aset->nama_alat ?? '-'),
            'Kondisi: ' . ($aset->status_kondisi ?? '-')
        );
    }, '#f0f8f5');
    ?>

<?php else: ?>

    <div style="padding:12px; text-align:center; color:#8e9bb0; font-size:12px; background:#f9fafb; border-radius:8px;">
        Tidak ada data khusus untuk halaman ini.
    </div>

<?php endif; ?>