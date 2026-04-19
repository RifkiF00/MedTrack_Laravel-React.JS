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

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="<?= BASEURL; ?>/css/dashboard.css">
</head>
<body>

<div class="dashboard-wrapper">

    <!-- SIDEBAR LEFT -->
    <aside class="sidebar-left">
        <div class="brand">
            <div class="brand-icon"><i class="bi bi-hospital"></i></div>
            <span>MedTrack</span>
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
            <?php if ($isIPSRS): ?>
                <li>
                    <a href="<?= BASEURL; ?>/maintenance">
                        <i class="bi bi-tools"></i> Preventive Maintenance
                    </a>
                </li>
            <?php endif; ?>

            <!-- DIREKTORI -->
            <?php if ($isIPSRS): ?>
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
                <input type="text" placeholder="Cari Kode Label / SN...">
            </div>

            <div class="icon-btn">
                <i class="bi bi-bell"></i>
                <span class="badge">3</span>
            </div>

            <img src="<?= BASEURL; ?>/uploads/assets/default-avatar.png" class="profile-pic">
        </div>

        <!-- SCHEDULE -->
        <?php if ($isIPSRS || $isLogistik): ?>
            <div class="schedule-section">
                <div class="section-title">
                    <h3><?= $isLogistik ? 'Jadwal Pengadaan' : 'Agenda Kalibrasi'; ?></h3>
                    <i class="bi bi-three-dots"></i>
                </div>

                <div class="calendar-strip">
                    <i class="bi bi-chevron-left"></i>
                    <span>April 2026</span>
                    <i class="bi bi-chevron-right"></i>
                </div>

                <div class="dates">
                    <div class="date-item"><span>S</span> 14</div>
                    <div class="date-item active"><span>R</span> 15</div>
                    <div class="date-item"><span>K</span> 16</div>
                    <div class="date-item"><span>J</span> 17</div>
                </div>
            </div>
        <?php endif; ?>

        <!-- TASK CARD -->
        <div class="task-card">

            <?php if ($isIPSRS): ?>
                <div class="task-img"></div>
                <h4>Kalibrasi EKG Monitor</h4>

            <?php elseif ($isUnit): ?>
                <div class="task-img" style="background-image: url('<?= BASEURL; ?>/img/qr-scan-bg.jpg');"></div>
                <h4>Lapor Malfungsi Alat</h4>

            <?php elseif ($isLogistik): ?>
                <div class="task-img" style="background-image: url('<?= BASEURL; ?>/img/box-bg.jpg');"></div>
                <h4>Verifikasi Sparepart</h4>
            <?php endif; ?>

        </div>

    </aside>
</div>

<script src="<?= BASEURL; ?>/js/dashboard.js"></script>
</body>
</html>