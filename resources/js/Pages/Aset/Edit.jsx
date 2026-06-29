import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Edit({ auth, aset, ruangans }) {
    const { data, setData, post, processing, errors } = useForm({
        _method: 'POST', // Handle Inertia multipart POST request mapping to update route
        kode_label: aset.kode_label || '',
        nama_alat: aset.nama_alat || '',
        kategori_aset: aset.kategori_aset || 'Medis',
        status_kondisi: aset.status_kondisi || 'Baik',
        id_ruang_saat_ini: aset.id_ruang_saat_ini || '',
        jumlah_unit: aset.jumlah_unit || 1,
        merk: aset.merk || '',
        model: aset.model || '',
        serial_number: aset.serial_number || '',
        no_sertifikat: aset.no_sertifikat || '',
        tgl_pengadaan: aset.tgl_pengadaan || '',
        tgl_kalibrasi_terakhir: aset.tgl_kalibrasi_terakhir || '',
        tgl_kadaluarsa_sertif: aset.tgl_kadaluarsa_sertif || '',
        harga_perolehan: aset.harga_perolehan || '',
        lokasi_fisik: aset.lokasi_fisik || '',
        keterangan: aset.keterangan || '',
        latitude: aset.latitude || '',
        longitude: aset.longitude || '',
        gambar_aset_file: null
    });

    const [leafletLoaded, setLeafletLoaded] = useState(false);
    const [imagePreview, setImagePreview] = useState(null);

    useEffect(() => {
        // Load Leaflet CSS & JS
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        link.id = 'leaflet-css';
        if (!document.getElementById('leaflet-css')) {
            document.head.appendChild(link);
        }

        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.async = true;
        script.id = 'leaflet-js';
        script.onload = () => setLeafletLoaded(true);

        if (!document.getElementById('leaflet-js')) {
            document.body.appendChild(script);
        } else {
            setLeafletLoaded(true);
        }

        return () => {
            const css = document.getElementById('leaflet-css');
            const js = document.getElementById('leaflet-js');
            if (css) css.remove();
            if (js) js.remove();
        };
    }, []);

    useEffect(() => {
        if (!leafletLoaded || !window.L) return;

        const L = window.L;
        // Default to asset location if exists, otherwise Kuningan RS coords
        const lat = parseFloat(data.latitude) || -6.9744;
        const lng = parseFloat(data.longitude) || 108.4773;

        const map = L.map('leaflet-edit-map').setView([lat, lng], 15);

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        let marker = L.marker([lat, lng], { draggable: true }).addTo(map);

        const updateCoordinates = (newLat, newLng) => {
            setData(prev => ({
                ...prev,
                latitude: newLat.toFixed(8),
                longitude: newLng.toFixed(8)
            }));
        };

        marker.on('dragend', (e) => {
            const position = marker.getLatLng();
            updateCoordinates(position.lat, position.lng);
        });

        map.on('click', (e) => {
            const clickedLat = e.latlng.lat;
            const clickedLng = e.latlng.lng;
            marker.setLatLng([clickedLat, clickedLng]);
            updateCoordinates(clickedLat, clickedLng);
        });

        return () => {
            map.remove();
        };
    }, [leafletLoaded]);

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        if (file) {
            setData('gambar_aset_file', file);
            const reader = new FileReader();
            reader.onload = (event) => {
                setImagePreview(event.target.result);
            };
            reader.readAsDataURL(file);
        }
    };

    const submit = (e) => {
        e.preventDefault();
        // Laravels route: Route::post('/aset/{id}') mapped to update method
        post(route('aset.update', aset.id_aset), {
            forceFormData: true,
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-extrabold text-2xl text-slate-800 leading-tight">Edit Aset Medis</h2>
                        <p className="text-xs text-slate-500 mt-1">Perbarui informasi detail, sertifikasi kalibrasi, dan titik koordinat GPS aset</p>
                    </div>
                    <Link
                        href={route('aset.index')}
                        className="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-sm font-semibold transition shadow-sm"
                    >
                        Batal
                    </Link>
                </div>
            }
        >
            <Head title={`Edit Aset - ${aset.nama_alat}`} />

            <div className="space-y-6 pb-8">
                <form onSubmit={submit} className="bg-white border border-slate-100 shadow-sm rounded-2xl overflow-hidden p-8 space-y-8">
                    
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                        {/* Kode Label */}
                        <div>
                            <label className="block text-sm font-bold text-slate-700">Kode Label / Register Aset *</label>
                            <input
                                type="text"
                                value={data.kode_label}
                                onChange={(e) => setData('kode_label', e.target.value)}
                                className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required
                            />
                            {errors.kode_label && <p className="text-rose-500 text-xs mt-1">{errors.kode_label}</p>}
                        </div>

                        {/* Nama Alat */}
                        <div>
                            <label className="block text-sm font-bold text-slate-700">Nama Alat Medis *</label>
                            <input
                                type="text"
                                value={data.nama_alat}
                                onChange={(e) => setData('nama_alat', e.target.value)}
                                className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                required
                            />
                            {errors.nama_alat && <p className="text-rose-500 text-xs mt-1">{errors.nama_alat}</p>}
                        </div>

                        {/* Kategori Aset */}
                        <div>
                            <label className="block text-sm font-bold text-slate-700">Kategori Aset *</label>
                            <select
                                value={data.kategori_aset}
                                onChange={(e) => setData('kategori_aset', e.target.value)}
                                className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="Medis">Medis</option>
                                <option value="Sarpras">Sarpras</option>
                                <option value="IT">IT</option>
                            </select>
                            {errors.kategori_aset && <p className="text-rose-500 text-xs mt-1">{errors.kategori_aset}</p>}
                        </div>

                        {/* Merk */}
                        <div>
                            <label className="block text-sm font-bold text-slate-700">Merk / Brand</label>
                            <input
                                type="text"
                                value={data.merk}
                                onChange={(e) => setData('merk', e.target.value)}
                                className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>

                        {/* Model */}
                        <div>
                            <label className="block text-sm font-bold text-slate-700">Model / Type</label>
                            <input
                                type="text"
                                value={data.model}
                                onChange={(e) => setData('model', e.target.value)}
                                className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>

                        {/* Serial Number */}
                        <div>
                            <label className="block text-sm font-bold text-slate-700">Serial Number (SN)</label>
                            <input
                                type="text"
                                value={data.serial_number}
                                onChange={(e) => setData('serial_number', e.target.value)}
                                className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>

                        {/* Status Kondisi */}
                        <div>
                            <label className="block text-sm font-bold text-slate-700">Status Kondisi *</label>
                            <select
                                value={data.status_kondisi}
                                onChange={(e) => setData('status_kondisi', e.target.value)}
                                className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="Baik">Baik</option>
                                <option value="Rusak_Ringan">Rusak Ringan</option>
                                <option value="Rusak_Berat">Rusak Berat</option>
                                <option value="Maintenance">Maintenance</option>
                                <option value="Gudang">Di Gudang</option>
                                <option value="Pensiun">Pensiun</option>
                            </select>
                        </div>

                        {/* Penempatan Ruangan */}
                        <div>
                            <label className="block text-sm font-bold text-slate-700">Penempatan Ruangan</label>
                            <select
                                value={data.id_ruang_saat_ini}
                                onChange={(e) => setData('id_ruang_saat_ini', e.target.value)}
                                className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            >
                                <option value="">Pilih Ruangan</option>
                                {ruangans.map((r) => (
                                    <option key={r.id_ruang} value={r.id_ruang}>{r.nama_ruang}</option>
                                ))}
                            </select>
                        </div>

                        {/* Jumlah Unit */}
                        <div>
                            <label className="block text-sm font-bold text-slate-700">Jumlah Unit</label>
                            <input
                                type="number"
                                min="1"
                                value={data.jumlah_unit}
                                onChange={(e) => setData('jumlah_unit', e.target.value)}
                                className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                            />
                        </div>
                    </div>

                    {/* Geolocation GPS Coordinate picker */}
                    <div className="border-t border-slate-100 pt-6 space-y-4">
                        <div>
                            <h4 className="font-bold text-slate-800 text-sm">📍 Pemetaan Koordinat GPS (Auto Klik dari Peta)</h4>
                            <p className="text-xs text-slate-400 mt-1">Gunakan klik di peta atau geser marker untuk menyesuaikan koordinat lokasi alat medis.</p>
                        </div>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label className="block text-sm font-semibold text-slate-600">Latitude</label>
                                <input
                                    type="text"
                                    value={data.latitude}
                                    readOnly
                                    placeholder="Auto-fill koordinat..."
                                    className="mt-1.5 w-full border-0 bg-slate-100 rounded-xl px-4 py-2.5 text-sm cursor-not-allowed select-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-slate-600">Longitude</label>
                                <input
                                    type="text"
                                    value={data.longitude}
                                    readOnly
                                    placeholder="Auto-fill koordinat..."
                                    className="mt-1.5 w-full border-0 bg-slate-100 rounded-xl px-4 py-2.5 text-sm cursor-not-allowed select-none"
                                />
                            </div>
                            <div className="md:col-span-3">
                                <div className="border border-slate-200 rounded-xl overflow-hidden relative z-0">
                                    <div id="leaflet-edit-map" className="w-full h-64 bg-slate-50"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Certification / Calibration Section */}
                    <div className="border-t border-slate-100 pt-6 space-y-4">
                        <h4 className="font-bold text-slate-800 text-sm">📋 Jadwal Kalibrasi & Sertifikasi</h4>
                        <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label className="block text-sm font-semibold text-slate-600">Nomor Sertifikat</label>
                                <input
                                    type="text"
                                    value={data.no_sertifikat}
                                    onChange={(e) => setData('no_sertifikat', e.target.value)}
                                    className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-slate-600">Tanggal Kalibrasi Terakhir</label>
                                <input
                                    type="date"
                                    value={data.tgl_kalibrasi_terakhir}
                                    onChange={(e) => setData('tgl_kalibrasi_terakhir', e.target.value)}
                                    className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-slate-600">Tanggal Kadaluarsa Sertifikat</label>
                                <input
                                    type="date"
                                    value={data.tgl_kadaluarsa_sertif}
                                    onChange={(e) => setData('tgl_kadaluarsa_sertif', e.target.value)}
                                    className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    {/* Image & Extra info */}
                    <div className="border-t border-slate-100 pt-6 space-y-6">
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {/* Left: Current & New Image preview */}
                            <div>
                                <label className="block text-sm font-bold text-slate-700">📸 Gambar Aset Medis</label>
                                <input
                                    type="file"
                                    onChange={handleFileChange}
                                    className="mt-1.5 w-full border border-slate-200 bg-slate-50 rounded-xl px-4 py-2 text-sm focus:outline-none file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                {errors.gambar_aset_file && <p className="text-rose-500 text-xs mt-1">{errors.gambar_aset_file}</p>}
                                
                                <div className="grid grid-cols-2 gap-4 mt-4">
                                    {aset.gambar_aset && (
                                        <div>
                                            <span className="text-xs text-slate-400 block mb-1">Gambar Saat Ini</span>
                                            <img src={aset.gambar_aset} alt="Saat ini" className="w-full h-32 object-cover rounded-xl border border-slate-200" />
                                        </div>
                                    )}
                                    {imagePreview && (
                                        <div>
                                            <span className="text-xs text-slate-400 block mb-1">Gambar Baru</span>
                                            <img src={imagePreview} alt="Preview" className="w-full h-32 object-cover rounded-xl border border-slate-200" />
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Right: Deskripsi */}
                            <div>
                                <label className="block text-sm font-bold text-slate-700">Catatan / Deskripsi Keterangan</label>
                                <textarea
                                    value={data.keterangan}
                                    onChange={(e) => setData('keterangan', e.target.value)}
                                    rows="6"
                                    className="mt-1.5 w-full border-0 bg-slate-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
                        </div>
                    </div>

                    {/* Submit buttons */}
                    <div className="border-t border-slate-100 pt-6 flex justify-end space-x-3">
                        <Link
                            href={route('aset.index')}
                            className="px-5 py-2.5 bg-slate-50 hover:bg-slate-150 border border-slate-200 rounded-xl text-sm font-semibold text-slate-700 transition"
                        >
                            Batal
                        </Link>
                        <button
                            type="submit"
                            disabled={processing}
                            className="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition shadow-sm"
                        >
                            Simpan Perubahan
                        </button>
                    </div>

                </form>
            </div>
        </AuthenticatedLayout>
    );
}
