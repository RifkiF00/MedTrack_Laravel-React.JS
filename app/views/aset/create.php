<div class="container" style="max-width: 900px; margin: 30px auto; padding: 0 20px;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:15px; flex-wrap:wrap;">
        <div>
            <h2 style="margin:0;">Tambah Aset</h2>
            <p style="margin:8px 0 0; color:#666;">Input data alat medis, sarpras, dan IT baru.</p>
        </div>
        <a href="<?= BASEURL; ?>/aset" style="padding:10px 16px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px;">
            Kembali
        </a>
    </div>

    <?php if (!empty($data['errors'])): ?>
        <div style="margin-bottom:20px; padding:14px 16px; border-radius:8px; background:#fdecec; color:#b42318; border:1px solid #f5c2c7;">
            <strong>Periksa input berikut:</strong>
            <ul style="margin:10px 0 0 18px;">
                <?php foreach ($data['errors'] as $error): ?>
                    <li><?= escape($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= BASEURL; ?>/aset/store" method="POST" style="background:#fff; border:1px solid #ddd; border-radius:12px; padding:24px;">
        <input type="hidden" name="csrf_token" value="<?= getCSRFToken(); ?>">

        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <div>
                <label>Kode Label</label>
                <input
                    type="text"
                    name="kode_label"
                    value="<?= escape($data['old']['kode_label'] ?? ''); ?>"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div>
                <label>Nama Alat</label>
                <input
                    type="text"
                    name="nama_alat"
                    value="<?= escape($data['old']['nama_alat'] ?? ''); ?>"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div>
                <label>Kategori Aset</label>
                <select name="kategori_aset" style="width:100%; padding:10px; margin-top:6px;">
                    <?php $selectedKategori = $data['old']['kategori_aset'] ?? 'Medis'; ?>
                    <option value="Medis" <?= $selectedKategori === 'Medis' ? 'selected' : ''; ?>>Medis</option>
                    <option value="Sarpras" <?= $selectedKategori === 'Sarpras' ? 'selected' : ''; ?>>Sarpras</option>
                    <option value="IT" <?= $selectedKategori === 'IT' ? 'selected' : ''; ?>>IT</option>
                </select>
            </div>

            <div>
                <label>Jumlah Unit</label>
                <input
                    type="number"
                    name="jumlah_unit"
                    min="1"
                    value="<?= escape($data['old']['jumlah_unit'] ?? 1); ?>"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div>
                <label>Merk</label>
                <input
                    type="text"
                    name="merk"
                    value="<?= escape($data['old']['merk'] ?? ''); ?>"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div>
                <label>Model</label>
                <input
                    type="text"
                    name="model"
                    value="<?= escape($data['old']['model'] ?? ''); ?>"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div>
                <label>Serial Number</label>
                <input
                    type="text"
                    name="serial_number"
                    value="<?= escape($data['old']['serial_number'] ?? ''); ?>"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div>
                <label>No. Sertifikat</label>
                <input
                    type="text"
                    name="no_sertifikat"
                    value="<?= escape($data['old']['no_sertifikat'] ?? ''); ?>"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div>
                <label>Tanggal Pengadaan</label>
                <input
                    type="date"
                    name="tgl_pengadaan"
                    value="<?= escape($data['old']['tgl_pengadaan'] ?? ''); ?>"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div>
                <label>Tgl Kalibrasi Terakhir</label>
                <input
                    type="date"
                    name="tgl_kalibrasi_terakhir"
                    value="<?= escape($data['old']['tgl_kalibrasi_terakhir'] ?? ''); ?>"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div>
                <label>Harga Perolehan</label>
                <input
                    type="number"
                    step="0.01"
                    name="harga_perolehan"
                    value="<?= escape($data['old']['harga_perolehan'] ?? ''); ?>"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div>
                <label>Status Kondisi</label>
                <select name="status_kondisi" style="width:100%; padding:10px; margin-top:6px;">
                    <option value="">-- Pilih Kondisi --</option>
                    <?php
                    $kondisiList = ['Gudang', 'Baik', 'Rusak_Ringan', 'Rusak_Berat', 'Maintenance', 'Pensiun'];
                    $selectedKondisi = $data['old']['status_kondisi'] ?? '';
                    foreach ($kondisiList as $kondisi):
                    ?>
                        <option value="<?= $kondisi; ?>" <?= $selectedKondisi === $kondisi ? 'selected' : ''; ?>>
                            <?= $kondisi; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="grid-column:1 / -1;">
                <label>Ruangan Saat Ini</label>
                <select name="id_ruang_saat_ini" style="width:100%; padding:10px; margin-top:6px;">
                    <option value="">-- Pilih Ruangan --</option>
                    <?php foreach ($data['ruangan'] as $ruang): ?>
                        <option
                            value="<?= $ruang->id_ruang; ?>"
                            <?= (($data['old']['id_ruang_saat_ini'] ?? '') == $ruang->id_ruang) ? 'selected' : ''; ?>
                        >
                            <?= escape($ruang->nama_ruang); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="grid-column:1 / -1;">
                <label>Lokasi Fisik</label>
                <input
                    type="text"
                    name="lokasi_fisik"
                    value="<?= escape($data['old']['lokasi_fisik'] ?? ''); ?>"
                    placeholder="Contoh: Bed 1 ICU / Koridor Poli / Area Genset"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <!-- GPS -->
            <div style="grid-column:1 / -1; background:#f8fafc; border:1px solid #e5e7eb; border-radius:12px; padding:16px;">
                <div style="margin-bottom:12px;">
                    <h4 style="margin:0 0 6px 0;">Lokasi GPS (Opsional)</h4>
                    <p style="margin:0; color:#6b7280; font-size:14px;">
                        Direkomendasikan untuk aset tetap/utilitas seperti AC outdoor, genset, panel listrik, trafo, IPAL, pompa, dan central oxygen.
                    </p>
                </div>

                <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                    <div>
                        <label>Latitude</label>
                        <input
                            type="text"
                            name="latitude"
                            id="latitude"
                            value="<?= escape($data['old']['latitude'] ?? ''); ?>"
                            placeholder="Contoh: -6.8734567"
                            style="width:100%; padding:10px; margin-top:6px;"
                        >
                    </div>

                    <div>
                        <label>Longitude</label>
                        <input
                            type="text"
                            name="longitude"
                            id="longitude"
                            value="<?= escape($data['old']['longitude'] ?? ''); ?>"
                            placeholder="Contoh: 108.4567890"
                            style="width:100%; padding:10px; margin-top:6px;"
                        >
                    </div>
                </div>

                <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
                    <button
                        type="button"
                        onclick="getLocation()"
                        style="padding:10px 14px; background:#0dcaf0; color:#000; border:none; border-radius:8px; cursor:pointer;"
                    >
                        Ambil Lokasi Saat Ini
                    </button>

                    <span id="gps-status" style="align-self:center; color:#6b7280; font-size:14px;">
                        Belum mengambil lokasi.
                    </span>
                </div>

                <div style="margin-top:16px;">
                    <div style="display:flex; justify-content:space-between; align-items:center; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
                        <div style="font-size:14px; color:#6b7280;">
                            Klik peta untuk memilih titik lokasi, atau geser marker.
                        </div>
                    </div>

                    <div id="map-picker" style="height:360px; border-radius:12px; overflow:hidden; border:1px solid #d1d5db;"></div>
                </div>
            </div>

            <div style="grid-column:1 / -1;">
                <label>Gambar Aset</label>
                <input
                    type="text"
                    name="gambar_aset"
                    value="<?= escape($data['old']['gambar_aset'] ?? ''); ?>"
                    placeholder="Contoh: ac-5pk.jpg"
                    style="width:100%; padding:10px; margin-top:6px;"
                >
            </div>

            <div style="grid-column:1 / -1;">
                <label>Keterangan</label>
                <textarea
                    name="keterangan"
                    rows="4"
                    style="width:100%; padding:10px; margin-top:6px;"
                ><?= escape($data['old']['keterangan'] ?? ''); ?></textarea>
            </div>

            <!-- GAMBAR ASET -->
            <div style="grid-column:1 / -1; margin-top:20px; padding-top:20px; border-top:1px solid #ddd;">
                <h4 style="margin:0 0 16px 0; font-size:15px; font-weight:600;">📸 Upload Gambar Aset</h4>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px;">
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <label style="font-weight:600; font-size:13px;">Pilih Gambar</label>
                        <input type="file" id="gambar_aset_input" name="gambar_aset" accept="image/*" style="padding:10px; border:1px solid #ddd; border-radius:8px; background:#fff; cursor:pointer;">
                        <div style="font-size:12px; color:#666;">Format: JPG, PNG, GIF (Max: 5MB)</div>
                    </div>
                    <div id="image-preview-container" style="display:none;">
                        <label style="font-weight:600; font-size:13px;">Preview</label>
                        <div style="width:100%; aspect-ratio:1; border:2px dashed #ddd; border-radius:8px; display:flex; align-items:center; justify-content:center; background:#f9f9f9; overflow:hidden;">
                            <img id="preview-image" src="" alt="Preview" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top:20px; display:flex; justify-content:flex-end; gap:10px;">
            <a href="<?= BASEURL; ?>/aset" style="padding:10px 16px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px;">Batal</a>
            <button type="submit" style="padding:10px 16px; background:#0d6efd; color:#fff; border:none; border-radius:8px; cursor:pointer;">
                Simpan Aset
            </button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
function getLocation() {
    const status = document.getElementById('gps-status');

    if (!navigator.geolocation) {
        status.textContent = 'Browser tidak mendukung geolocation.';
        status.style.color = '#dc3545';
        return;
    }

    status.textContent = 'Mengambil lokasi...';
    status.style.color = '#2563eb';

    navigator.geolocation.getCurrentPosition(
        function(position) {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            document.getElementById('latitude').value = lat.toFixed(7);
            document.getElementById('longitude').value = lng.toFixed(7);

            if (window.assetMap && window.assetMarker) {
                window.assetMap.setView([lat, lng], 19);
                window.assetMarker.setLatLng([lat, lng]);
            }

            status.textContent = 'Lokasi berhasil diambil.';
            status.style.color = '#198754';
        },
        function() {
            status.textContent = 'Gagal mengambil lokasi. Pastikan izin lokasi diaktifkan.';
            status.style.color = '#dc3545';
        },
        {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        }
    );
}

document.addEventListener('DOMContentLoaded', function () {
    const latInput = document.getElementById('latitude');
    const lngInput = document.getElementById('longitude');
    const status = document.getElementById('gps-status');

    const defaultLat = latInput.value ? parseFloat(latInput.value) : -6.7320000;
    const defaultLng = lngInput.value ? parseFloat(lngInput.value) : 108.4850000;

    const map = L.map('map-picker').setView([defaultLat, defaultLng], 18);
    window.assetMap = map;

    const streetLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap'
    });

    const satelliteLayer = L.tileLayer(
        'https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}',
        {
            attribution: 'Tiles &copy; Esri'
        }
    );

    satelliteLayer.addTo(map);

    L.control.layers(
        {
            'Peta': streetLayer,
            'Satelit': satelliteLayer
        },
        {},
        { collapsed: false }
    ).addTo(map);

    const marker = L.marker([defaultLat, defaultLng], { draggable: true }).addTo(map);
    window.assetMarker = marker;

    function updateInputs(lat, lng, message = 'Titik lokasi dipilih dari peta.') {
        latInput.value = Number(lat).toFixed(7);
        lngInput.value = Number(lng).toFixed(7);
        status.textContent = message;
        status.style.color = '#198754';
    }

    map.on('click', function (e) {
        const lat = e.latlng.lat;
        const lng = e.latlng.lng;
        marker.setLatLng([lat, lng]);
        updateInputs(lat, lng);
    });

    marker.on('dragend', function () {
        const pos = marker.getLatLng();
        updateInputs(pos.lat, pos.lng, 'Marker digeser, koordinat diperbarui.');
    });

    latInput.addEventListener('change', function () {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 19);
        }
    });

    lngInput.addEventListener('change', function () {
        const lat = parseFloat(latInput.value);
        const lng = parseFloat(lngInput.value);
        if (!isNaN(lat) && !isNaN(lng)) {
            marker.setLatLng([lat, lng]);
            map.setView([lat, lng], 19);
        }
    });

    if (latInput.value && lngInput.value) {
        status.textContent = 'Koordinat awal dimuat ke peta.';
        status.style.color = '#198754';
    }

    // Preview Gambar
    document.getElementById('gambar_aset_input').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        // Validasi ukuran file (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Ukuran gambar maksimal 5MB');
            this.value = '';
            document.getElementById('image-preview-container').style.display = 'none';
            return;
        }

        // Validasi tipe file
        if (!file.type.startsWith('image/')) {
            alert('File harus berupa gambar');
            this.value = '';
            document.getElementById('image-preview-container').style.display = 'none';
            return;
        }

        // Preview gambar
        const reader = new FileReader();
        reader.onload = function(event) {
            document.getElementById('preview-image').src = event.target.result;
            document.getElementById('image-preview-container').style.display = 'grid';
        };
        reader.readAsDataURL(file);
    });
});
</script>