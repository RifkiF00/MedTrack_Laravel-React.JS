<?php
$role = $_SESSION['role'] ?? '';
$namaUser = $_SESSION['nama_lengkap'] ?? 'User';
$contentView = $data['content_view'] ?? '';
$hidePageHeader = $data['hide_page_header'] ?? false;

// helper role
$isIPSRS = ($role === 'Staf_IPSRS' || $role === 'Staf_Logistik');
$isLogistik = ($role === 'Staf_Logistik');
$isUnit = ($role === 'Unit_RS');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['judul'] ?? 'MedTrack IPSRS'; ?></title>

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASEURL; ?>/css/dashboard.css">
</head>
<body>

<div class="dashboard-wrapper">

    <!-- SIDEBAR LEFT -->
    <aside class="sidebar-left">
        <div class="brand">
            <div class="brand-icon" style="display: flex; align-items: center; justify-content: center; width: 78px; height: 52px; background: #ffffff; border-radius: 12px; overflow: hidden; flex-shrink: 0;">
                <img src="<?= BASEURL; ?>/uploads/assets/logo-rs.png" alt="RS Logo" style="width: 94%; height: 94%; object-fit: contain;">
            </div>
            <span style="font-size: 24px; font-weight: 700;">MedTrack</span>
        </div>

        <ul class="nav-menu">

            <!-- DASHBOARD -->
            <li class="<?= $contentView === 'dashboard/index' ? 'active' : ''; ?>">
                <a href="<?= BASEURL; ?>/dashboard">
                    <i class="bi bi-grid-1x2"></i> Dasbor Utama
                </a>
            </li>

            <!-- MASTER ASET -->
            <?php if ($isIPSRS || $isLogistik): ?>
                <li class="<?= strpos($contentView, 'aset/') === 0 ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/aset">
                        <i class="bi bi-box-seam"></i> Master Alkes & Sarpras
                    </a>
                </li>
            <?php endif; ?>

            <!-- MUTASI -->
            <?php if ($isIPSRS || $isLogistik): ?>
                <li>
                    <a href="<?= BASEURL; ?>/mutasi">
                        <i class="bi bi-arrow-left-right"></i> Mutasi Ruangan
                    </a>
                </li>
            <?php endif; ?>

            <!-- WORK ORDER -->
            <?php if ($isIPSRS || $isUnit): ?>
                <li>
                    <a href="<?= BASEURL; ?>/workorder">
                        <i class="bi bi-ticket-detailed"></i> E-Work Order (WO)
                    </a>
                </li>
            <?php endif; ?>

            <!-- PREVENTIVE -->
            <?php if ($role === 'Staf_IPSRS'): ?>
                <li>
                    <a href="<?= BASEURL; ?>/maintenance">
                        <i class="bi bi-tools"></i> Preventive Maintenance
                    </a>
                </li>
            <?php endif; ?>

            <!-- DIREKTORI -->
            <?php if ($role === 'Staf_IPSRS'): ?>
                <li>
                    <a href="<?= BASEURL; ?>/direktori">
                        <i class="bi bi-building"></i> Direktori Unit & SDM
                    </a>
                </li>
            <?php endif; ?>

            <!-- DOKUMEN -->
            <?php if ($isIPSRS || $isLogistik): ?>
                <li>
                    <a href="<?= BASEURL; ?>/dokumen">
                        <i class="bi bi-file-earmark-medical"></i> Dokumen Mutu
                    </a>
                </li>
            <?php endif; ?>

            <!-- SCAN QR -->
            <?php if ($isUnit): ?>
                <li class="<?= $contentView === 'aset/scan' ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/aset/scan">
                        <i class="bi bi-qr-code-scan"></i> Scan QR Aset
                    </a>
                </li>
            <?php endif; ?>

            <!-- DETAIL ASET (UNIT) -->
            <?php if ($isUnit): ?>
                <li class="<?= $contentView === 'aset/list_unit' ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/aset/listunit">
                        <i class="bi bi-info-circle"></i> Detail Aset
                    </a>
                </li>
            <?php endif; ?>

            <!-- LOGOUT -->
            <li style="margin-top: 30px;">
                <a href="<?= BASEURL; ?>/auth/logout"
                   style="color: #ffbaba;"
                   onclick="return confirm('Apakah Anda yakin ingin keluar?')">
                    <i class="bi bi-box-arrow-left"></i> Keluar Sistem
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <p>&copy; MedTrack 2026</p>
            <span>RS Hasna Medika Kuningan</span>
        </div>
    </aside>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <?php if (!$hidePageHeader): ?>
            <div class="header-mid">
                <div class="header-titles">
                    <h1><?= $data['page_heading'] ?? 'Dashboard'; ?></h1>
                    <p><?= $data['page_subheading'] ?? 'Sistem Manajemen Aset Medis'; ?></p>
                </div>

                <button id="toggleRight" class="toggle-btn">
                    <i class="bi bi-layout-sidebar-reverse"></i>
                </button>
            </div>
        <?php endif; ?>

        <?php
        if (!empty($contentView)) {
            require_once '../app/views/' . $contentView . '.php';
        }
        ?>
    </main>

    <!-- SIDEBAR RIGHT -->
    <aside class="sidebar-right">

        <div class="top-right">
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="sidebarSearch" placeholder="Cari Kode Label / SN..." style="border: none; background: transparent; outline: none; margin-left: 10px; width: 100%; color: var(--text-main); font-size: 13px;">
            </div>

            <div class="icon-btn" id="notificationBell" style="position: relative; cursor: pointer;" onclick="toggleNotificationPanel()">
                <i class="bi bi-bell"></i>
                <?php if (!empty($data['notification_count']) && $data['notification_count'] > 0): ?>
                <span class="badge"><?= $data['notification_count'] > 99 ? '99+' : $data['notification_count']; ?></span>
                <?php endif; ?>
            </div>

            <!-- PROFILE SECTION - Clickable -->
            <div style="display: flex; align-items: center; gap: 8px; cursor: pointer; padding: 8px; border-radius: 10px; transition: all 0.2s;"
                 onclick="window.location='<?= BASEURL; ?>/profile'"
                 onmouseover="this.style.background='#f0f4f8'"
                 onmouseout="this.style.background='transparent'">
                <!-- PROFILE IMAGE -->
                <div style="width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; overflow: hidden; border: 2px solid #3d6aff; background: #f0f4f8; cursor: pointer; position: relative;">
                    <?php
                        $profile_photo = null;
                        $userId = !empty($data['user']->id_user) ? $data['user']->id_user : ($_SESSION['user_id'] ?? '0');
                        $base_dir = dirname(dirname(dirname(__DIR__)));
                        $upload_dir = $base_dir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profiles';
                        $glob_pattern = $upload_dir . DIRECTORY_SEPARATOR . 'profile_' . $userId . '.*';
                        $files = glob($glob_pattern);
                        if (!empty($files)) {
                            $filename = basename($files[0]);
                            $profile_photo = BASEURL . '/uploads/profiles/' . $filename . '?t=' . time();
                        }
                    ?>
                    <?php if ($profile_photo): ?>
                        <img src="<?= $profile_photo; ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: 50%;" title="<?= escape($namaUser); ?>">
                    <?php else: ?>
                        <div style="font-size: 20px; cursor: pointer;">👤</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- SCHEDULE - ALL ROLES -->
        <div class="schedule-section">
            <div class="section-title">
                <h3><?= $isLogistik ? 'Jadwal Pengadaan' : ($isUnit ? 'Jadwal Unit' : 'Agenda Kalibrasi'); ?></h3>
            </div>

            <div class="calendar-strip">
                <i class="bi bi-chevron-left"></i>
                <span>April 2026</span>
                <i class="bi bi-chevron-right"></i>
            </div>

            <!-- Calendar dengan event indicators -->
            <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 30px;">
                <?php
                    // Get maintenance and WO schedules
                    $jadwal_maintenance = $data['jadwal_minggu_maintenance'] ?? [];
                    $jadwal_wo = $data['jadwal_minggu_wo'] ?? [];

                    // Create array of dates with event counts
                    $dates_with_events = [];
                    foreach ($jadwal_maintenance as $m) {
                        $dates_with_events[$m->tanggal]['maintenance'] = ($dates_with_events[$m->tanggal]['maintenance'] ?? 0) + $m->total_jadwal;
                    }
                    foreach ($jadwal_wo as $w) {
                        $dates_with_events[$w->tanggal]['wo'] = ($dates_with_events[$w->tanggal]['wo'] ?? 0) + $w->total_wo;
                    }

                    // Generate 8 dates starting from today
                    for ($i = 0; $i < 8; $i++) {
                        $date = date('Y-m-d', strtotime("+$i days"));
                        $day_name = date('D', strtotime($date)); // Sun, Mon, Tue...
                        $day_num = date('d', strtotime($date));
                        $has_events = isset($dates_with_events[$date]);
                        $maint_count = $dates_with_events[$date]['maintenance'] ?? 0;
                        $wo_count = $dates_with_events[$date]['wo'] ?? 0;
                        $is_today = ($date === date('Y-m-d'));
                ?>
                <div style="text-align: center;">
                    <div style="font-size: 11px; color: #8e9bb0; margin-bottom: 4px; font-weight: 600;"><?= substr($day_name, 0, 1); ?></div>
                    <div style="position: relative; display: inline-block;">
                        <div style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center; border-radius: 10px; font-weight: 700; font-size: 14px; color: #1a2b56; background: <?= $is_today ? '#cd1601' : '#f0f4f8'; ?>; color: <?= $is_today ? 'white' : '#1a2b56'; ?>;">
                            <?= $day_num; ?>
                        </div>
                        <?php if ($has_events): ?>
                            <div style="position: absolute; bottom: -8px; left: 50%; transform: translateX(-50%); display: flex; gap: 3px;">
                                <?php if ($maint_count > 0): ?>
                                    <div style="width: 6px; height: 6px; border-radius: 50%; background: #f59e0b;"></div>
                                <?php endif; ?>
                                <?php if ($wo_count > 0): ?>
                                    <div style="width: 6px; height: 6px; border-radius: 50%; background: #ef4444;"></div>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php } ?>
            </div>
        </div>

        <!-- SCHEDULE DISPLAY - Show details for selected date (default: today) -->
        <div class="schedule-display" style="margin-bottom: 30px;">
            <div style="section-title">
                <h3 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0 0 12px 0;">Jadwal <?= date('d M Y'); ?></h3>
            </div>

            <!-- MAINTENANCE TASKS FOR TODAY -->
            <?php
                $maintenance_today = $data['jadwal_hari_ini_maintenance'] ?? [];
                $wo_today = $data['jadwal_hari_ini_wo'] ?? [];
            ?>

            <?php if (!empty($maintenance_today) || !empty($wo_today)): ?>

                <!-- Maintenance Section -->
                <?php if (!empty($maintenance_today)): ?>
                <div style="margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <span style="font-size: 14px;">📅</span>
                        <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Maintenance</h4>
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
                <?php endif; ?>

                <!-- Work Orders Section -->
                <?php if (!empty($wo_today)): ?>
                <div style="margin-bottom: 24px;">
                    <div style="display: flex; align-items: center; gap: 8px; margin-bottom: 12px;">
                        <span style="font-size: 14px;">🔴</span>
                        <h4 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Work Order</h4>
                    </div>
                    <div style="background: #fff5f5; border-radius: 8px; overflow: hidden;">
                        <?php foreach ($wo_today as $wo): ?>
                        <div style="padding: 10px 12px; border-bottom: 1px solid #ffecec; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#ffecec'" onmouseout="this.style.background='transparent'">
                            <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($wo->kode_label); ?> - <?= escape($wo->nama_alat ?? 'Equipment'); ?></div>
                            <div style="color: #cd1601; font-size: 11px; font-weight: 600;"><?= $wo->tingkat_urgensi; ?></div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <div style="padding: 12px; text-align: center; color: #8e9bb0; font-size: 12px; background: #f9fafb; border-radius: 8px;">
                    Tidak ada jadwal untuk hari ini
                </div>
            <?php endif; ?>
        </div>

        <!-- TASK CARD IMAGE -->
        <div class="task-card">
            <?php if ($isIPSRS): ?>
                <div class="task-img"></div>

            <?php elseif ($isUnit): ?>
                <div class="task-img" style="background: linear-gradient(135deg, #3d6aff 0%, #2952cc 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">📱</div>

            <?php elseif ($isLogistik): ?>
                <div class="task-img" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">📦</div>
            <?php endif; ?>
        </div>

        <!-- CALIBRATION SCHEDULE -->
        <?php if (($data['content_view'] ?? '') === 'dashboard/index'): ?>
            <div style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                    <h3 style="font-size: 13px; font-weight: 700; color: #1a2b56; margin: 0;">Jadwal Kalibrasi Hari Ini</h3>
                    <a href="<?= BASEURL; ?>/maintenance/create" style="font-size: 11px; color: #3d6aff; text-decoration: none; font-weight: 600; cursor: pointer;">+ Tambah</a>
                </div>

                <?php
                    $maintenance_today = $data['jadwal_hari_ini_maintenance'] ?? [];
                ?>

                <?php if (!empty($maintenance_today)): ?>
                <div style="background: #fafbfc; border-radius: 8px; overflow: hidden;">
                    <?php foreach ($maintenance_today as $m): ?>
                    <div style="padding: 10px 12px; border-bottom: 1px solid #e5e7eb; font-size: 12px; color: #1a2b56; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f5f7fa'" onmouseout="this.style.background='transparent'">
                        <div style="font-weight: 600; margin-bottom: 2px;"><?= escape($m->nama_item); ?></div>
                        <div style="color: #8e9bb0; font-size: 11px; margin-bottom: 2px;">📍 <?= escape($m->lokasi ?? 'N/A'); ?></div>
                        <div style="color: <?= $m->status_pelaksanaan === 'Terselesaikan' ? '#10b981' : '#f59e0b'; ?>; font-size: 11px; font-weight: 600;"><?= $m->status_pelaksanaan; ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <div style="padding: 12px; text-align: center; color: #8e9bb0; font-size: 12px; background: #f9fafb; border-radius: 8px;">
                    Tidak ada jadwal kalibrasi untuk hari ini
                </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        </div>

        <!-- TASK CARD IMAGE -->
        <div class="task-card">
            <?php if ($isIPSRS): ?>
                <div class="task-img"></div>

            <?php elseif ($isUnit): ?>
                <div class="task-img" style="background: linear-gradient(135deg, #3d6aff 0%, #2952cc 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">📱</div>

            <?php elseif ($isLogistik): ?>
                <div class="task-img" style="background: linear-gradient(135deg, #10b981 0%, #059669 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 48px;">📦</div>
            <?php endif; ?>
        </div>

        <!-- DYNAMIC ROLE-BASED SECTIONS -->
        <?php require_once __DIR__ . '/sidebar-right.php'; ?>

    </aside>

    <!-- NOTIFICATION PANEL -->
    <div id="notificationPanel" style="display: none; position: fixed; top: 70px; right: 30px; width: 380px; background: white; border-radius: 12px; box-shadow: 0 10px 40px rgba(0,0,0,0.1); z-index: 1000; max-height: 500px; overflow-y: auto;">
        <div style="padding: 16px; border-bottom: 1px solid #e5e7eb; background: #f9fafb;">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h3 style="font-size: 14px; font-weight: 700; color: #1a2b56; margin: 0;">Notifikasi</h3>
                <span style="font-size: 12px; background: #cd1601; color: white; padding: 2px 8px; border-radius: 12px; font-weight: 600;">
                    <?= !empty($data['notification_count']) ? $data['notification_count'] : '0'; ?>
                </span>
            </div>
        </div>

        <div style="padding: 0;">
            <?php if (!empty($data['notifications'])): ?>
                <?php foreach ($data['notifications'] as $notif): ?>
                <div style="padding: 12px 16px; border-bottom: 1px solid #f0f0f0; cursor: pointer; transition: 0.2s;" onmouseover="this.style.background='#f9fafb'" onmouseout="this.style.background='transparent'">
                    <div style="display: flex; gap: 12px;">
                        <div style="font-size: 24px; flex-shrink: 0;"><?= $notif['icon']; ?></div>
                        <div style="flex: 1; min-width: 0;">
                            <div style="font-size: 12px; font-weight: 700; color: #1a2b56; margin-bottom: 2px;"><?= $notif['title']; ?></div>
                            <div style="font-size: 11px; color: #8e9bb0; word-break: break-word;"><?= $notif['message']; ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding: 24px 16px; text-align: center; color: #8e9bb0; font-size: 12px;">
                    Tidak ada notifikasi baru
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Overlay untuk close notification panel -->
    <div id="notificationOverlay" style="display: none; position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 999;"></div>

</div>

<script src="<?= BASEURL; ?>/js/dashboard.js"></script>
</body>
</html>