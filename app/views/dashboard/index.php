<?php 
$role = $data['role'] ?? 'User';
$namaUser = $data['nama'] ?? 'User';

$isUnit = ($role === 'Unit_RS');

$total = $data['total_aset'] ?? 0;
$total_medis = $data['total_medis'] ?? 0;
$total_sarpras = $data['total_sarpras'] ?? 0;
$total_it = $data['total_it'] ?? 0;

$baik = $data['baik'] ?? 0;
$rusak_ringan = $data['rusak_ringan'] ?? 0;
$rusak_berat = $data['rusak_berat'] ?? 0;
$maintenance = $data['maintenance'] ?? 0;
$gudang = $data['gudang'] ?? 0;

$ruanganLabels = $data['ruangan_labels'] ?? [];
$ruanganTotals = $data['ruangan_totals'] ?? [];

$perlu_penanganan = $rusak_ringan + $rusak_berat + $maintenance;
$aktif_operasional = $baik;

$persen_baik = $total > 0 ? round(($baik / $total) * 100, 1) : 0;
$persen_penanganan = $total > 0 ? round(($perlu_penanganan / $total) * 100, 1) : 0;
?>

<!-- HERO CARD -->
<div class="card" style="padding: 24px; margin-bottom: 20px;">
    <div style="display:flex; justify-content:space-between; align-items:flex-start; gap:20px; flex-wrap:wrap;">
        <div>
            <h2 style="margin:0 0 8px 0;">Selamat Datang, <?= escape($namaUser) ?></h2>
            <p style="margin:0; color:#7b8aa0;">
                Status: <strong style="color:#1d4ed8;"><?= escape($role) ?></strong>
            </p>

            <?php if ($isUnit): ?>
                <p style="margin:8px 0 0; color:#64748b;">
                    Ringkasan aset pada unit/ruangan Anda.
                </p>
            <?php endif; ?>
        </div>

        <div style="display:flex; gap:12px; flex-wrap:wrap;">
            <div style="background:#f8fafc; padding:12px 16px; border-radius:12px; min-width:110px;">
                <div style="font-size:12px; color:#7b8aa0;">Total</div>
                <div style="font-size:22px; font-weight:700; color:#0f172a;"><?= $total ?></div>
            </div>

            <div style="background:#ecfdf5; padding:12px 16px; border-radius:12px; min-width:110px;">
                <div style="font-size:12px; color:#7b8aa0;">Baik</div>
                <div style="font-size:22px; font-weight:700; color:#166534;"><?= $baik ?></div>
            </div>

            <div style="background:#fff7ed; padding:12px 16px; border-radius:12px; min-width:110px;">
                <div style="font-size:12px; color:#7b8aa0;">Rusak</div>
                <div style="font-size:22px; font-weight:700; color:#c2410c;"><?= $rusak_ringan + $rusak_berat ?></div>
            </div>

            <div style="background:#eff6ff; padding:12px 16px; border-radius:12px; min-width:110px;">
                <div style="font-size:12px; color:#7b8aa0;">Maintenance</div>
                <div style="font-size:22px; font-weight:700; color:#1d4ed8;"><?= $maintenance ?></div>
            </div>
        </div>
    </div>
</div>

<!-- PROGRESS + SUMMARY -->
<div style="display:grid; grid-template-columns:1.2fr 1fr; gap:20px; margin-bottom:20px;">
    <div class="card" style="padding:20px;">
        <h3 style="margin-top:0;"><?= $isUnit ? 'Indeks Kelayakan Aset Unit' : 'Indeks Kelayakan Aset' ?></h3>

        <div style="margin-bottom:16px;">
            <div style="display:flex; justify-content:space-between; font-size:14px; margin-bottom:6px;">
                <span>Laik Pakai (Baik)</span>
                <strong><?= $baik ?> dari <?= $total ?> (<?= $persen_baik ?>%)</strong>
            </div>
            <div style="background:#e5e7eb; height:10px; border-radius:999px; overflow:hidden;">
                <div style="width:<?= $persen_baik ?>%; background:#2563eb; height:10px; border-radius:999px;"></div>
            </div>
        </div>

        <div>
            <div style="display:flex; justify-content:space-between; font-size:14px; margin-bottom:6px;">
                <span>Perlu Penanganan</span>
                <strong><?= $perlu_penanganan ?> dari <?= $total ?> (<?= $persen_penanganan ?>%)</strong>
            </div>
            <div style="background:#e5e7eb; height:10px; border-radius:999px; overflow:hidden;">
                <div style="width:<?= $persen_penanganan ?>%; background:#dc2626; height:10px; border-radius:999px;"></div>
            </div>
        </div>
    </div>

    <div class="card" style="padding:20px;">
        <h3 style="margin-top:0;"><?= $isUnit ? 'Ringkasan Kondisi Unit' : 'Ringkasan Kondisi' ?></h3>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
            <div style="background:#f8fafc; padding:14px; border-radius:12px;">
                <div style="font-size:12px; color:#7b8aa0;">Baik</div>
                <div style="font-size:24px; font-weight:700; color:#166534;"><?= $baik ?></div>
            </div>

            <div style="background:#f8fafc; padding:14px; border-radius:12px;">
                <div style="font-size:12px; color:#7b8aa0;">Gudang</div>
                <div style="font-size:24px; font-weight:700; color:#475569;"><?= $gudang ?></div>
            </div>

            <div style="background:#f8fafc; padding:14px; border-radius:12px;">
                <div style="font-size:12px; color:#7b8aa0;">Rusak Ringan</div>
                <div style="font-size:24px; font-weight:700; color:#d97706;"><?= $rusak_ringan ?></div>
            </div>

            <div style="background:#f8fafc; padding:14px; border-radius:12px;">
                <div style="font-size:12px; color:#7b8aa0;">Rusak Berat</div>
                <div style="font-size:24px; font-weight:700; color:#dc2626;"><?= $rusak_berat ?></div>
            </div>

            <div style="background:#f8fafc; padding:14px; border-radius:12px; grid-column:1 / -1;">
                <div style="font-size:12px; color:#7b8aa0;">Maintenance</div>
                <div style="font-size:24px; font-weight:700; color:#0284c7;"><?= $maintenance ?></div>
            </div>
        </div>
    </div>
</div>

<!-- 4 CHARTS -->
<div style="display:grid; grid-template-columns:repeat(2, 1fr); gap:20px;">
    <div class="card" style="padding:20px;">
        <h3 style="margin-top:0; margin-bottom:12px;"><?= $isUnit ? 'Kondisi Aset Unit' : 'Kondisi Aset' ?></h3>
        <div style="height:240px;">
            <canvas id="chartKondisi"></canvas>
        </div>
    </div>

    <div class="card" style="padding:20px;">
        <h3 style="margin-top:0; margin-bottom:12px;"><?= $isUnit ? 'Komposisi Kategori Unit' : 'Komposisi Kategori' ?></h3>
        <div style="height:240px;">
            <canvas id="chartKategori"></canvas>
        </div>
    </div>

    <div class="card" style="padding:20px;">
        <h3 style="margin-top:0; margin-bottom:12px;"><?= $isUnit ? 'Operasional vs Penanganan Unit' : 'Operasional vs Penanganan' ?></h3>
        <div style="height:240px;">
            <canvas id="chartOperasional"></canvas>
        </div>
    </div>

    <div class="card" style="padding:20px;">
        <h3 style="margin-top:0; margin-bottom:12px;"><?= $isUnit ? 'Aset pada Unit Anda' : 'Aset per Ruangan' ?></h3>
        <div style="height:240px;">
            <canvas id="chartStatusBar"></canvas>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const chartKondisi = document.getElementById('chartKondisi');
new Chart(chartKondisi, {
    type: 'doughnut',
    data: {
        labels: ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Maintenance', 'Gudang'],
        datasets: [{
            data: [<?= $baik ?>, <?= $rusak_ringan ?>, <?= $rusak_berat ?>, <?= $maintenance ?>, <?= $gudang ?>],
            backgroundColor: ['#22c55e', '#f59e0b', '#ef4444', '#0ea5e9', '#64748b'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});

const chartKategori = document.getElementById('chartKategori');
new Chart(chartKategori, {
    type: 'pie',
    data: {
        labels: ['Medis', 'Sarpras', 'IT'],
        datasets: [{
            data: [<?= $total_medis ?>, <?= $total_sarpras ?>, <?= $total_it ?>],
            backgroundColor: ['#2563eb', '#f97316', '#7c3aed'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } }
    }
});

const chartOperasional = document.getElementById('chartOperasional');
new Chart(chartOperasional, {
    type: 'bar',
    data: {
        labels: ['Operasional', 'Perlu Penanganan', 'Gudang'],
        datasets: [{
            label: 'Jumlah Aset',
            data: [<?= $aktif_operasional ?>, <?= $perlu_penanganan ?>, <?= $gudang ?>],
            backgroundColor: ['#2563eb', '#dc2626', '#64748b'],
            borderRadius: 8
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { beginAtZero: true, ticks: { precision: 0 } }
        }
    }
});

const chartStatusBar = document.getElementById('chartStatusBar');
new Chart(chartStatusBar, {
    type: 'line',
    data: {
        labels: <?= json_encode($ruanganLabels) ?>,
        datasets: [{
            label: 'Jumlah Aset',
            data: <?= json_encode($ruanganTotals) ?>,
            borderColor: '#2563eb',
            backgroundColor: 'rgba(37, 99, 235, 0.12)',
            fill: true,
            tension: 0.4,
            pointRadius: 4,
            pointHoverRadius: 6
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { display: false } },
        scales: {
            y: { 
                beginAtZero: true,
                ticks: { precision: 0 }
            }
        }
    }
});
</script>