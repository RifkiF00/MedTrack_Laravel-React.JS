<?php
$role = $_SESSION['role'] ?? '';
$namaUser = $_SESSION['nama_lengkap'] ?? 'User';
$contentView = $data['content_view'] ?? '';
$hidePageHeader = $data['hide_page_header'] ?? false;

$isIPSRS = ($role === 'Staf_IPSRS');
$isLogistik = ($role === 'Staf_Logistik');
$isUnit = ($role === 'Unit_RS');
$canAccessAsset = ($isIPSRS || $isLogistik);
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
        <div class="brand" style="display:flex; align-items:center; gap:8px; padding:15px 5px;">
            <div class="brand-icon" style="display:flex; align-items:center; justify-content:center; width:52px; height:38px; background:#ffffff; border-radius:10px; overflow:hidden; flex-shrink:0; box-shadow:0 2px 5px rgba(0,0,0,0.15);">
                <img src="<?= BASEURL; ?>/uploads/assets/logo-rs.png" alt="RS Logo" style="width:90%; height:90%; object-fit:contain;">
            </div>
            <span style="font-size:21px; font-weight:700; color:#ffffff; letter-spacing:0.5px;">MedTrack</span>
        </div>

        <ul class="nav-menu">

            <li class="<?= $contentView === 'dashboard/index' ? 'active' : ''; ?>">
                <a href="<?= BASEURL; ?>/dashboard">
                    <i class="bi bi-grid-1x2"></i> Dasbor Utama
                </a>
            </li>

            <?php if ($canAccessAsset): ?>
                <li class="<?= strpos($contentView, 'aset/') === 0 ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/aset">
                        <i class="bi bi-box-seam"></i> Master Alkes & Sarpras
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($canAccessAsset): ?>
                <li class="<?= strpos($contentView, 'mutasi/') === 0 ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/mutasi">
                        <i class="bi bi-arrow-left-right"></i> Mutasi Ruangan
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($isIPSRS || $isUnit): ?>
                <li class="<?= strpos($contentView, 'workorder/') === 0 ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/workorder">
                        <i class="bi bi-ticket-detailed"></i> E-Work Order (WO)
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($isIPSRS): ?>
                <li class="<?= strpos($contentView, 'maintenance/') === 0 ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/maintenance">
                        <i class="bi bi-tools"></i> Preventive Maintenance
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($isIPSRS): ?>
                <li class="<?= strpos($contentView, 'direktori/') === 0 ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/direktori">
                        <i class="bi bi-building"></i> Direktori Unit & SDM
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($canAccessAsset): ?>
                <li class="<?= strpos($contentView, 'dokumen/') === 0 ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/dokumen">
                        <i class="bi bi-file-earmark-medical"></i> Dokumen Mutu
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($isUnit): ?>
                <li class="<?= $contentView === 'aset/scan' ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/aset/scan">
                        <i class="bi bi-qr-code-scan"></i> Scan QR Aset
                    </a>
                </li>

                <li class="<?= $contentView === 'aset/list_unit' ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/aset/listunit">
                        <i class="bi bi-info-circle"></i> Detail Aset
                    </a>
                </li>

                <li class="<?= strpos($contentView, 'mutasi/') === 0 ? 'active' : ''; ?>">
                    <a href="<?= BASEURL; ?>/mutasi">
                        <i class="bi bi-arrow-left-right"></i> Permintaan Mutasi
                    </a>
                </li>
            <?php endif; ?>

            <li style="margin-top:30px;">
                <a href="<?= BASEURL; ?>/auth/logout"
                   style="color:#ffbaba;"
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
                <input
                    type="text"
                    id="sidebarSearch"
                    placeholder="Cari Kode Label / SN..."
                    style="border:none; background:transparent; outline:none; margin-left:10px; width:100%; color:var(--text-main); font-size:13px;"
                >
            </div>

            <div class="icon-btn" id="notificationBell" style="position:relative; cursor:pointer;" onclick="toggleNotificationPanel()">
                <i class="bi bi-bell"></i>
                <?php if (!empty($data['notification_count']) && $data['notification_count'] > 0): ?>
                    <span class="badge"><?= $data['notification_count'] > 99 ? '99+' : $data['notification_count']; ?></span>
                <?php endif; ?>
            </div>

            <div
                style="display:flex; align-items:center; gap:8px; cursor:pointer; padding:8px; border-radius:10px; transition:all 0.2s;"
                onclick="window.location='<?= BASEURL; ?>/profile'"
                onmouseover="this.style.background='#f0f4f8'"
                onmouseout="this.style.background='transparent'"
            >
                <div style="width:40px; height:40px; border-radius:50%; display:flex; align-items:center; justify-content:center; overflow:hidden; border:2px solid #3d6aff; background:#f0f4f8; position:relative;">
                    <?php
                    $profilePhoto = null;
                    $userId = $_SESSION['user_id'] ?? '0';
                    $baseDir = dirname(dirname(dirname(__DIR__)));
                    $uploadDir = $baseDir . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'profiles';
                    $globPattern = $uploadDir . DIRECTORY_SEPARATOR . 'profile_' . $userId . '.*';
                    $files = glob($globPattern);

                    if (!empty($files)) {
                        $filename = basename($files[0]);
                        $profilePhoto = BASEURL . '/uploads/profiles/' . $filename . '?t=' . time();
                    }
                    ?>

                    <?php if ($profilePhoto): ?>
                        <img src="<?= $profilePhoto; ?>" style="width:100%; height:100%; object-fit:cover; border-radius:50%;" title="<?= escape($namaUser); ?>">
                    <?php else: ?>
                        <div style="font-size:20px;">👤</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- KALENDER - SELALU MUNCUL -->
        <div class="schedule-section">
            <div class="section-title">
                <h3><?= $isLogistik ? 'Jadwal Pengadaan' : ($isUnit ? 'Jadwal Unit' : 'Agenda Kalibrasi'); ?></h3>
            </div>

            <div class="calendar-strip">
                <i class="bi bi-chevron-left"></i>
                <span><?= date('F Y'); ?></span>
                <i class="bi bi-chevron-right"></i>
            </div>

            <div class="dates">
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <?php
                    $date = strtotime("+$i days");
                    $hari = ['Min','Sen','Sel','Rab','Kam','Jum','Sab'][date('w', $date)];
                    $tanggal = date('d', $date);
                    $active = ($i === 0) ? 'active' : '';
                    ?>
                    <div class="date-item <?= $active; ?>">
                        <span><?= substr($hari, 0, 1); ?></span>
                        <?= $tanggal; ?>
                    </div>
                <?php endfor; ?>
            </div>
        </div>

        <!-- GAMBAR RS / TASK CARD - SELALU MUNCUL -->
        <div class="task-card" style="margin-bottom:24px;">
            <div class="task-img"></div>

            <h4>
                <?php if ($contentView === 'aset/index'): ?>
                    Monitoring Aset
                <?php elseif ($contentView === 'aset/scan'): ?>
                    Scan QR Aset
                <?php elseif ($contentView === 'workorder/index'): ?>
                    Work Order Aktif
                <?php endif; ?>
            </h4>

            <div class="task-details">
                <div class="detail-row">
                    <i class="bi bi-calendar"></i> <?= date('d M Y'); ?>
                </div>
                <div class="detail-row">
                    <i class="bi bi-geo-alt"></i> RS Hasna Medika
                </div>
            </div>

            <div class="task-actions">
                <button class="btn-outline" onclick="openRescheduleModal()">Reschedule</button>
                <a href="<?= BASEURL; ?>/maintenance" class="btn-solid" style="text-decoration:none;">
                    Detail <i class="bi bi-arrow-right"></i>
                </a>
            </div>
        </div>

        <!-- RESCHEDULE MODAL -->
        <div id="rescheduleModal" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; background:rgba(0,0,0,0.5); z-index:2000; align-items:center; justify-content:center;">
            <div style="background:white; border-radius:12px; padding:24px; max-width:400px; width:90%; box-shadow:0 20px 60px rgba(0,0,0,0.3);">
                <h3 style="margin:0 0 16px 0; color:#1a2b56; font-size:16px; font-weight:700;">Reschedule Maintenance</h3>

                <form method="POST" action="<?= BASEURL; ?>/maintenance/reschedule" style="display:flex; flex-direction:column; gap:12px;">
                    <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <label style="font-size:12px; font-weight:600; color:#475569;">Pilih Maintenance Item</label>
                        <select name="id_pemeliharaan" required style="padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
                            <option value="">-- Pilih Item --</option>
                            <?php
                            $maintenanceModel = $this->model('Maintenance_model');
                            $allMaintenance = $maintenanceModel->getAllPemeliharaan();
                            foreach ($allMaintenance as $m):
                            ?>
                                <option value="<?= $m->id_pemeliharaan; ?>"><?= escape($m->nama_item); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div style="display:flex; flex-direction:column; gap:6px;">
                        <label style="font-size:12px; font-weight:600; color:#475569;">Tanggal Baru</label>
                        <input type="date" name="tgl_rencana" required style="padding:10px 12px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px;">
                    </div>

                    <div style="display:flex; gap:8px; margin-top:12px;">
                        <button type="button" onclick="closeRescheduleModal()" style="flex:1; padding:10px; background:#e5e7eb; color:#1a2b56; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
                            Batal
                        </button>
                        <button type="submit" style="flex:1; padding:10px; background:#3d6aff; color:white; border:none; border-radius:8px; font-weight:600; cursor:pointer;">
                            Reschedule
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <script>
        function openRescheduleModal() {
            document.getElementById('rescheduleModal').style.display = 'flex';
        }

        function closeRescheduleModal() {
            document.getElementById('rescheduleModal').style.display = 'none';
        }

        // Close modal when clicking outside
        document.getElementById('rescheduleModal').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
        </script>

        <?php require_once __DIR__ . '/sidebar-right.php'; ?>

    </aside>

    <!-- NOTIFICATION PANEL -->
    <div id="notificationPanel" style="display:none; position:fixed; top:70px; right:30px; width:380px; background:white; border-radius:12px; box-shadow:0 10px 40px rgba(0,0,0,0.1); z-index:1000; max-height:500px; overflow-y:auto;">
        <div style="padding:16px; border-bottom:1px solid #e5e7eb; background:#f9fafb;">
            <div style="display:flex; justify-content:space-between; align-items:center;">
                <h3 style="font-size:14px; font-weight:700; color:#1a2b56; margin:0;">Notifikasi</h3>
                <span style="font-size:12px; background:#cd1601; color:white; padding:2px 8px; border-radius:12px; font-weight:600;">
                    <?= !empty($data['notification_count']) ? $data['notification_count'] : '0'; ?>
                </span>
            </div>
        </div>

        <div style="padding:0;">
            <?php if (!empty($data['notifications'])): ?>
                <?php foreach ($data['notifications'] as $notif): ?>
                    <a href="<?= $notif['action_url'] ?? 'javascript:void(0)'; ?>"
                       style="display:block; padding:12px 16px; border-bottom:1px solid #f0f0f0; cursor:pointer; transition:0.2s; text-decoration:none; color:inherit;"
                       onmouseover="this.style.background='#f9fafb'"
                       onmouseout="this.style.background='transparent'">
                        <div style="display:flex; gap:12px;">
                            <div style="font-size:24px; flex-shrink:0;"><?= $notif['icon']; ?></div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:12px; font-weight:700; color:#1a2b56; margin-bottom:2px;">
                                    <?= escape($notif['title']); ?>
                                </div>
                                <div style="font-size:11px; color:#8e9bb0; word-break:break-word;">
                                    <?= escape($notif['message']); ?>
                                </div>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="padding:24px 16px; text-align:center; color:#8e9bb0; font-size:12px;">
                    Tidak ada notifikasi baru
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div id="notificationOverlay" style="display:none; position:fixed; top:0; left:0; right:0; bottom:0; z-index:999;"></div>

</div>

<script src="<?= BASEURL; ?>/js/dashboard.js"></script>
</body>
</html>