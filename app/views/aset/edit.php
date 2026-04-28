<div class="container" style="max-width: 1400px; margin: 0 auto; padding: 0 10px; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; color: #1e293b;">
    
    <div style="display:flex; justify-content:flex-end; margin-bottom:15px;">
        <a href="<?= BASEURL; ?>/aset" style="padding:10px 18px; background:#fff; color:#475569; text-decoration:none; border-radius:10px; font-weight: 600; font-size: 13px; border: 1px solid #e2e8f0; display: flex; align-items: center; gap: 8px; transition: all 0.2s;">
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="M19 12H5M12 19l-7-7 7-7"></path></svg>
            Kembali
        </a>
    </div>

    <form action="<?= BASEURL; ?>/aset/update/<?= $data['aset']->id_aset; ?>" method="POST" style="background:#fff; border: 1px solid #e2e8f0; border-radius:16px; padding:35px; box-shadow: 0 1px 3px rgba(0,0,0,0.05);">
        <input type="hidden" name="csrf_token" value="<?= generateCSRFToken(); ?>">

        <div style="display:grid; grid-template-columns: repeat(3, 1fr); gap:25px;">

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Kode Label</label>
                <input type="text" name="kode_label" value="<?= escape($data['aset']->kode_label ?? ''); ?>" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px; background:#fcfcfc;" required>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Nama Alat</label>
                <input type="text" name="nama_alat" value="<?= escape($data['aset']->nama_alat ?? ''); ?>" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px;" required>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Kategori Aset</label>
                <select name="kategori_aset" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px; background:#fff;">
                    <option value="Medis" <?= ($data['aset']->kategori_aset == 'Medis') ? 'selected' : '' ?>>Medis</option>
                    <option value="Sarpras" <?= ($data['aset']->kategori_aset == 'Sarpras') ? 'selected' : '' ?>>Sarpras</option>
                    <option value="IT" <?= ($data['aset']->kategori_aset == 'IT') ? 'selected' : '' ?>>IT</option>
                </select>
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Merk</label>
                <input type="text" name="merk" value="<?= escape($data['aset']->merk ?? ''); ?>" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px;">
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Model</label>
                <input type="text" name="model" value="<?= escape($data['aset']->model ?? ''); ?>" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px;">
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Serial Number</label>
                <input type="text" name="serial_number" value="<?= escape($data['aset']->serial_number ?? ''); ?>" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px;">
            </div>

            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Latitude (Auto dari Peta)</label>
                <input type="text" name="latitude" id="latitude" value="<?= escape($data['aset']->latitude ?? ''); ?>" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px; background:#f8fafc;" readonly>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Longitude (Auto dari Peta)</label>
                <input type="text" name="longitude" id="longitude" value="<?= escape($data['aset']->longitude ?? ''); ?>" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px; background:#f8fafc;" readonly>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Status Kondisi</label>
                <select name="status_kondisi" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px; background:#fff;">
                    <?php $kondisiList = ['Gudang','Baik','Rusak_Ringan','Rusak_Berat','Maintenance','Pensiun'];
                    foreach ($kondisiList as $k): ?>
                        <option value="<?= $k; ?>" <?= ($data['aset']->status_kondisi === $k) ? 'selected' : ''; ?>><?= str_replace('_', ' ', $k); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div style="grid-column: 1 / -1; margin-top: 20px; padding-top: 20px; border-top: 1px solid #e2e8f0;">
                <h4 style="font-weight: 600; color: #1e293b; margin-bottom: 15px; font-size: 15px;" id="kalibrasi-section">📋 Jadwal Kalibrasi</h4>
                <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label style="font-weight:600; color:#475569; font-size: 13px;">Tanggal Kalibrasi Terakhir</label>
                        <input type="date" name="tgl_kalibrasi_terakhir" value="<?= escape($data['aset']->tgl_kalibrasi_terakhir ?? ''); ?>" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px;">
                    </div>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label style="font-weight:600; color:#475569; font-size: 13px;">Nomor Sertifikat</label>
                        <input type="text" name="no_sertifikat" value="<?= escape($data['aset']->no_sertifikat ?? ''); ?>" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px;">
                    </div>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        <label style="font-weight:600; color:#475569; font-size: 13px;">Tanggal Kadaluarsa Sertifikat</label>
                        <input type="date" name="tgl_kadaluarsa_sertif" value="<?= escape($data['aset']->tgl_kadaluarsa_sertif ?? ''); ?>" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px;">
                    </div>
                </div>
            </div>

            <div style="grid-column: span 2; display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Penempatan Ruangan</label>
                <select name="id_ruang_saat_ini" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px; background:#fff;">
                    <?php foreach ($data['ruangan'] as $ruang): ?>
                        <option value="<?= $ruang->id_ruang; ?>" <?= ($data['aset']->id_ruang_saat_ini == $ruang->id_ruang) ? 'selected' : ''; ?>><?= escape($ruang->nama_ruang); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div style="display:flex; flex-direction:column; gap:8px; justify-content: flex-end;">
                <button type="button" onclick="getLocation()" style="padding:12px; background:#0ea5e9; color:#fff; border:none; border-radius:8px; cursor:pointer; font-size: 13px; font-weight:600; transition: 0.2s;">
                    📍 Update GPS Saat Ini
                </button>
            </div>

            <div style="grid-column: 1 / -1; margin-top:10px;">
                <label style="display:block; font-weight:600; color:#475569; font-size: 13px; margin-bottom:10px;">Lokasi Visual (Peta Satelit)</label>
                <div id="map-picker" style="height:500px; border-radius:12px; border:1px solid #cbd5e1; z-index:1; box-shadow: inset 0 2px 4px rgba(0,0,0,0.05);"></div>
            </div>

            <div style="grid-column: 1 / -1; display:flex; flex-direction:column; gap:8px;">
                <label style="font-weight:600; color:#475569; font-size: 13px;">Keterangan Tambahan</label>
                <textarea name="keterangan" rows="3" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size: 14px; resize:vertical;"><?= escape($data['aset']->keterangan ?? ''); ?></textarea>
            </div>

            <!-- GAMBAR ASET -->
            <div style="grid-column: 1 / -1; margin-top:20px; padding-top:20px; border-top:1px solid #e2e8f0;">
                <h4 style="font-weight:600; color:#1e293b; margin-bottom:15px; font-size:15px;">📸 Gambar Aset</h4>
                <div style="display:grid; grid-template-columns:1fr 1fr; gap:20px; align-items:start;">
                    <!-- Preview Gambar Saat Ini -->
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <label style="font-weight:600; color:#475569; font-size:13px;">Gambar Saat Ini</label>
                        <div style="width:100%; aspect-ratio:1; border:2px dashed #cbd5e1; border-radius:12px; display:flex; align-items:center; justify-content:center; background:#f8fafc; overflow:hidden;">
                            <?php if (!empty($data['aset']->gambar_aset)): ?>
                                <img id="current-image" src="<?= BASEURL; ?>/uploads/assets/<?= escape($data['aset']->gambar_aset); ?>" alt="Gambar Aset" style="width:100%; height:100%; object-fit:cover;">
                            <?php else: ?>
                                <div id="current-image" style="color:#9ca3af; text-align:center; padding:20px; font-size:13px;">Belum ada gambar</div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Upload & Preview Gambar Baru -->
                    <div style="display:flex; flex-direction:column; gap:10px;">
                        <label style="font-weight:600; color:#475569; font-size:13px;">Upload Gambar Baru</label>
                        <input type="file" id="gambar_aset_input" name="gambar_aset" accept="image/*" style="padding:12px 15px; border:1px solid #cbd5e1; border-radius:8px; font-size:14px; background:#fff; cursor:pointer;">
                        <div style="font-size:12px; color:#64748b;">Format: JPG, PNG, GIF (Max: 5MB)</div>
                        <div id="image-preview" style="width:100%; aspect-ratio:1; border:2px dashed #cbd5e1; border-radius:12px; display:none; align-items:center; justify-content:center; background:#f8fafc; overflow:hidden;">
                            <img id="preview-image" src="" alt="Preview" style="width:100%; height:100%; object-fit:cover;">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div style="margin-top:35px; display:flex; justify-content:flex-end; gap:15px; border-top: 1px solid #f1f5f9; padding-top:25px;">
            <a href="<?= BASEURL; ?>/aset" style="padding:12px 25px; color:#64748b; text-decoration:none; font-weight:600; font-size: 14px;">Batal</a>
            <button type="submit" style="padding:12px 45px; background:#2563eb; color:#fff; border:none; border-radius:10px; font-weight:600; font-size: 14px; cursor:pointer; box-shadow: 0 4px 6px -1px rgba(37,99,235,0.2); transition: 0.3s;">Simpan Perubahan</button>
        </div>
    </form>
</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const latIn = document.getElementById('latitude');
    const lngIn = document.getElementById('longitude');
    const dLat = latIn.value ? parseFloat(latIn.value) : -6.732;
    const dLng = lngIn.value ? parseFloat(lngIn.value) : 108.485;

    const map = L.map('map-picker', {
        center: [dLat, dLng],
        zoom: 19,
        scrollWheelZoom: false // Mencegah zoom tidak sengaja saat scroll halaman
    });

    // Layer Satelit Esri
    L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
        attribution: 'Esri World Imagery'
    }).addTo(map);

    const marker = L.marker([dLat, dLng], { draggable: true }).addTo(map);

    map.on('click', function(e) {
        marker.setLatLng(e.latlng);
        latIn.value = e.latlng.lat.toFixed(7);
        lngIn.value = e.latlng.lng.toFixed(7);
    });

    marker.on('dragend', function() {
        const p = marker.getLatLng();
        latIn.value = p.lat.toFixed(7);
        lngIn.value = p.lng.toFixed(7);
    });
});

function getLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(p) {
            const lt = p.coords.latitude;
            const lg = p.coords.longitude;
            document.getElementById('latitude').value = lt.toFixed(7);
            document.getElementById('longitude').value = lg.toFixed(7);
            alert("Berhasil menarik titik koordinat GPS!");
        });
    }
}

// Preview Gambar
document.getElementById('gambar_aset_input').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Validasi ukuran file (max 5MB)
    if (file.size > 5 * 1024 * 1024) {
        alert('Ukuran gambar maksimal 5MB');
        this.value = '';
        document.getElementById('image-preview').style.display = 'none';
        return;
    }

    // Validasi tipe file
    if (!file.type.startsWith('image/')) {
        alert('File harus berupa gambar');
        this.value = '';
        document.getElementById('image-preview').style.display = 'none';
        return;
    }

    // Preview gambar
    const reader = new FileReader();
    reader.onload = function(event) {
        document.getElementById('preview-image').src = event.target.result;
        document.getElementById('image-preview').style.display = 'flex';
    };
    reader.readAsDataURL(file);
});
</script>