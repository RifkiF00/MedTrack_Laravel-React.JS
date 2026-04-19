<?php
$aset = $data['aset ?? null;
$role = $data['role ?? 'Guest';
$backLink = ($role === 'Unit_RS') ? BASEURL . '/aset/listunit' : BASEURL . '/aset';
?>

<style>
@media print {
    body {
        background: #fff !important;
    }

    .sidebar-left,
    .sidebar-right,
    .header-mid,
    .toggle-btn,
    .print-hide,
    .task-card,
    .schedule-section,
    .top-right,
    .leaflet-control-container,
    .map-section {
        display: none !important;
    }

    .dashboard-wrapper,
    .main-content {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
    }

    .card {
        box-shadow: none !important;
        border: 1px solid #ddd !important;
        margin: 0 !important;
        padding: 16px !important;
        background: #fff !important;
    }

    .print-grid {
        display: grid !important;
        grid-template-columns: 1.7fr 1fr !important;
        gap: 20px !important;
        align-items: start !important;
    }

    .print-data-grid {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 12px !important;
    }

    .print-box {
        background: #fff !important;
        border: 1px solid #ddd !important;
        border-radius: 10px !important;
        padding: 12px !important;
        page-break-inside: avoid !important;
    }

    .print-full {
        grid-column: 1 / -1 !important;
    }

    img {
        max-width: 100% !important;
    }

    .print-title {
        display: block !important;
        margin-bottom: 16px !important;
    }
}
</style>

<?php if (!$aset): ?>
    <div class="card" style="padding:24px;">
        <h3>Data aset tidak ditemukan</h3>
        <a href="<?= $backLink; ?>"
           style="display:inline-block; margin-top:12px; padding:10px 16px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:8px;">
            Kembali ke Daftar Aset
        </a>
    </div>
<?php return; endif; ?>

<div class="card" style="padding:24px;">

    <div class="print-title" style="display:none;">
        <h2 style="margin:0 0 6px 0;">Detail Aset</h2>
        <p style="margin:0; color:#666;">Dokumen informasi aset inventaris MedTrack IPSRS</p>
    </div>

    <!-- HEADER -->
    <div class="print-hide" style="display:flex; justify-content:space-between; align-items:flex-start; gap:16px; flex-wrap:wrap; margin-bottom:24px;">
        <div>
            <h2 style="margin:0 0 8px 0;">Detail Aset</h2>
            <p style="margin:0; color:#6b7280;">
                Informasi lengkap aset berdasarkan data inventaris.
            </p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="<?= $backLink; ?>"
               style="padding:10px 14px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px;">
                ← Kembali
            </a>

            <button onclick="window.print()"
                    style="padding:10px 14px; background:#198754; color:#fff; border:none; border-radius:8px; cursor:pointer;">
                Print
            </button>
        </div>
    </div>

    <!-- TOP INFO -->
    <div class="print-grid" style="display:grid; grid-template-columns:2fr 1fr; gap:24px; align-items:start;">
        
        <!-- DATA -->
        <div class="print-data-grid" style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Kode Label</div>
                <div style="font-size:18px; font-weight:700;"><?= escape($aset->kode_label ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Nama Alat</div>
                <div style="font-size:18px; font-weight:700;"><?= escape($aset->nama_alat ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Kategori</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->kategori_aset ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Jumlah Unit</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->jumlah_unit ?? '1'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Merk</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->merk ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Model</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->model ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Serial Number</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->serial_number ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">No. Sertifikat</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->no_sertifikat ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Tanggal Pengadaan</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->tgl_pengadaan ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Kalibrasi Terakhir</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->tgl_kalibrasi_terakhir ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Harga Perolehan</div>
                <div style="font-size:16px; font-weight:600;">
                    <?= isset($aset->harga_perolehan) && $aset->harga_perolehan !== null
                        ? 'Rp ' . number_format((float)$aset->harga_perolehan, 0, ',', '.')
                        : '-'; ?>
                </div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Ruangan</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->nama_ruang ?? '-'); ?></div>
            </div>

            <div class="print-box print-full" style="background:#f8fafc; padding:16px; border-radius:12px; grid-column:1 / -1;">
                <div style="font-size:12px; color:#6b7280;">Lokasi Fisik</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->lokasi_fisik ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Latitude</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->latitude ?? '-'); ?></div>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px;">
                <div style="font-size:12px; color:#6b7280;">Longitude</div>
                <div style="font-size:16px; font-weight:600;"><?= escape($aset->longitude ?? '-'); ?></div>
            </div>

            <div class="print-box print-full" style="background:#f8fafc; padding:16px; border-radius:12px; grid-column:1 / -1;">
                <div style="font-size:12px; color:#6b7280;">Status Kondisi</div>
                <div style="margin-top:6px;">
                    <?php
                    $kondisi = $aset->status_kondisi ?? '';
                    $color = '#eef2ff';
                    if ($kondisi === 'Baik') $color = '#d1fae5';
                    elseif ($kondisi === 'Rusak Ringan') $color = '#fef3c7';
                    elseif ($kondisi === 'Rusak Berat') $color = '#fee2e2';
                    elseif ($kondisi === 'Maintenance') $color = '#e0f2fe';
                    elseif ($kondisi === 'Gudang') $color = '#f1f5f9';
                    ?>
                    <span style="display:inline-block; padding:8px 14px; border-radius:999px; background:<?= $color ?>; font-weight:600;">
                        <?= escape($kondisi ?: '-') ?>
                    </span>
                </div>
            </div>

            <div class="print-box print-full" style="background:#f8fafc; padding:16px; border-radius:12px; grid-column:1 / -1;">
                <div style="font-size:12px; color:#6b7280;">Keterangan</div>
                <div style="font-size:15px; line-height:1.6; margin-top:6px;">
                    <?= nl2br(escape($aset->keterangan ?? '-')); ?>
                </div>
            </div>

        </div>

        <!-- QR + GAMBAR -->
        <div style="display:flex; flex-direction:column; gap:16px;">
            
            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px; text-align:center;">
                <div style="font-size:13px; color:#6b7280; margin-bottom:10px;">QR Code Aset</div>

                <?php if (!empty($aset->file_qr_code)): ?>
                    <img src="<?= BASEURL; ?>/uploads/qr/<?= escape($aset->file_qr_code); ?>"
                         alt="QR Aset"
                         style="width:220px; max-width:100%; border-radius:12px; background:#fff; padding:10px; border:1px solid #e5e7eb;">
                    <div style="margin-top:10px; font-size:12px; color:#6b7280;">
                        Scan untuk membuka detail aset
                    </div>
                <?php else: ?>
                    <div style="padding:40px 20px; color:#9ca3af;">QR belum tersedia</div>
                <?php endif; ?>
            </div>

            <div class="print-box" style="background:#f8fafc; padding:16px; border-radius:12px; text-align:center;">
                <div style="font-size:13px; color:#6b7280; margin-bottom:10px;">Gambar Aset</div>

                <?php if (!empty($aset->gambar_aset)): ?>
                    <img src="<?= BASEURL; ?>/uploads/assets/<?= escape($aset->gambar_aset); ?>"
                         alt="Gambar Aset"
                         style="width:100%; max-width:280px; border-radius:12px; object-fit:cover;">
                <?php else: ?>
                    <div style="padding:40px 20px; color:#9ca3af;">Tidak ada gambar</div>
                <?php endif; ?>
            </div>

        </div>
    </div>

    <?php if (!empty($aset->latitude) && !empty($aset->longitude)): ?>
        <div class="map-section" style="margin-top:24px; background:#f8fafc; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
            <div style="display:flex; justify-content:space-between; align-items:center; gap:12px; flex-wrap:wrap; margin-bottom:12px;">
                <div>
                    <h3 style="margin:0 0 6px 0;">Lokasi Aset</h3>
                    <p style="margin:0; color:#6b7280; font-size:14px;">
                        Titik lokasi aset berdasarkan koordinat GPS.
                    </p>
                </div>

                <a href="https://www.google.com/maps?q=<?= escape($aset->latitude); ?>,<?= escape($aset->longitude); ?>"
                   target="_blank"
                   style="padding:10px 14px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:8px;">
                    Buka di Google Maps
                </a>
            </div>

            <div id="map" style="height:340px; border-radius:12px; overflow:hidden;"></div>
        </div>

        <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
        <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                var lat = <?= json_encode((float)$aset->latitude); ?>;
                var lng = <?= json_encode((float)$aset->longitude); ?>;

                var map = L.map('map').setView([lat, lng], 18);

                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                    attribution: '&copy; OpenStreetMap'
                }).addTo(map);

                L.marker([lat, lng])
                    .addTo(map)
                    .bindPopup("<?= escape($aset->nama_alat ?? 'Lokasi Aset'); ?>")
                    .openPopup();
            });
        </script>
    <?php endif; ?>
</div>