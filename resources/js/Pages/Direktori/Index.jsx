import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { useState, useEffect } from 'react';

export default function Index({ auth, ruangans, sdmList, canCrud, currentRole }) {
    const [activeTab, setActiveTab] = useState('units');

    // Modals control
    const [showRuanganModal, setShowRuanganModal] = useState(false);
    const [showSdmModal, setShowSdmModal] = useState(false);
    const [showKontakModal, setShowKontakModal] = useState(false);

    const [editingRuangan, setEditingRuangan] = useState(null);
    const [editingSdm, setEditingSdm] = useState(null);
    const [editingKontakSdm, setEditingKontakSdm] = useState(null);

    // Detail Ruangan states
    const [showDetailRuanganModal, setShowDetailRuanganModal] = useState(false);
    const [selectedDetailRuangan, setSelectedDetailRuangan] = useState(null);
    const [leafletLoaded, setLeafletLoaded] = useState(false);

    const openRuanganDetail = (ruangan) => {
        setSelectedDetailRuangan(ruangan);
        setShowDetailRuanganModal(true);
    };

    // Load Leaflet resources dynamically when either modal is shown
    useEffect(() => {
        if (!showDetailRuanganModal && !showRuanganModal) return;

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        link.id = 'leaflet-css';
        if (!document.getElementById('leaflet-css')) {
            document.head.appendChild(link);
        }

        const style = document.createElement('style');
        style.id = 'leaflet-custom-styles';
        style.innerHTML = `
            .custom-div-icon {
                background: transparent !important;
                border: none !important;
            }
        `;
        if (!document.getElementById('leaflet-custom-styles')) {
            document.head.appendChild(style);
        }

        const script = document.createElement('script');
        script.src = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.js';
        script.async = true;
        script.id = 'leaflet-js';
        script.onload = () => {
            setLeafletLoaded(true);
        };

        if (!document.getElementById('leaflet-js')) {
            document.body.appendChild(script);
        } else {
            setLeafletLoaded(true);
        }
    }, [showDetailRuanganModal, showRuanganModal]);

    // Map Picker: showRuanganModal
    useEffect(() => {
        if (!leafletLoaded || !window.L || !showRuanganModal) return;

        const L = window.L;
        const initLat = parseFloat(ruanganForm.data.latitude) || -6.985;
        const initLng = parseFloat(ruanganForm.data.longitude) || 108.485;

        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Esri'
        });

        const streetsLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'OSM'
        });

        const map = L.map('picker-map-container', {
            center: [initLat, initLng],
            zoom: 16,
            layers: [satelliteLayer]
        });

        const baseMaps = {
            "Satelit": satelliteLayer,
            "Jalan": streetsLayer
        };
        L.control.layers(baseMaps).addTo(map);

        const redIcon = L.divIcon({
            html: `
                <div class="relative flex items-center justify-center">
                    <span class="absolute inline-flex h-8 w-8 rounded-full bg-rose-400 opacity-75 animate-pulse"></span>
                    <svg class="w-10 h-10 text-rose-600 relative z-10" style="filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.35));" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
            `,
            className: 'custom-div-icon',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        let marker = null;
        if (ruanganForm.data.latitude && ruanganForm.data.longitude) {
            marker = L.marker([initLat, initLng], { icon: redIcon, draggable: true }).addTo(map);
            marker.on('dragend', (e) => {
                const newPos = e.target.getLatLng();
                ruanganForm.setData(data => ({
                    ...data,
                    latitude: newPos.lat.toFixed(6),
                    longitude: newPos.lng.toFixed(6)
                }));
            });
        }

        map.on('click', (e) => {
            const clickLat = e.latlng.lat;
            const clickLng = e.latlng.lng;
            ruanganForm.setData(data => ({
                ...data,
                latitude: clickLat.toFixed(6),
                longitude: clickLng.toFixed(6)
            }));

            if (marker) {
                marker.setLatLng(e.latlng);
            } else {
                marker = L.marker(e.latlng, { icon: redIcon, draggable: true }).addTo(map);
                marker.on('dragend', (ev) => {
                    const newPos = ev.target.getLatLng();
                    ruanganForm.setData(data => ({
                        ...data,
                        latitude: newPos.lat.toFixed(6),
                        longitude: newPos.lng.toFixed(6)
                    }));
                });
            }
        });

        return () => {
            map.remove();
        };
    }, [leafletLoaded, showRuanganModal]);

    // Map Viewer: showDetailRuanganModal
    useEffect(() => {
        if (!leafletLoaded || !window.L || !showDetailRuanganModal || !selectedDetailRuangan) return;

        const L = window.L;
        const lat = selectedDetailRuangan.latitude;
        const lng = selectedDetailRuangan.longitude;

        if (!lat || !lng) return;

        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Esri'
        });

        const streetsLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: 'OSM'
        });

        const map = L.map('detail-room-map', {
            center: [lat, lng],
            zoom: 17,
            layers: [satelliteLayer]
        });

        const baseMaps = {
            "Satelit": satelliteLayer,
            "Jalan": streetsLayer
        };
        L.control.layers(baseMaps).addTo(map);

        const redIcon = L.divIcon({
            html: `
                <div class="relative flex items-center justify-center">
                    <span class="absolute inline-flex h-8 w-8 rounded-full bg-rose-400 opacity-75 animate-pulse"></span>
                    <svg class="w-10 h-10 text-rose-600 relative z-10" style="filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.35));" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
            `,
            className: 'custom-div-icon',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });

        L.marker([lat, lng], { icon: redIcon })
            .addTo(map)
            .bindPopup(`<b>${selectedDetailRuangan.nama_ruang}</b><br/>${selectedDetailRuangan.lokasi_gedung || ''}`)
            .openPopup();

        return () => {
            map.remove();
        };
    }, [leafletLoaded, showDetailRuanganModal, selectedDetailRuangan]);

    // Form: Ruangan
    const ruanganForm = useForm({
        nama_ruang: '',
        lokasi_gedung: '',
        kategori: 'Medis',
        latitude: '',
        longitude: '',
        foto_file: null
    });

    // Form: SDM
    const sdmForm = useForm({
        username: '',
        email: '',
        password: '',
        nama_lengkap: '',
        role: 'Unit_RS',
        id_ruang: '',
        nip: '',
        no_hp: '',
        status: 'Aktif'
    });

    // Form: Kontak
    const kontakForm = useForm({
        kontak_darurat_1: '',
        kontak_darurat_2: '',
        kontak_darurat_3: ''
    });

    // Actions: Ruangan
    const openRuanganAdd = () => {
        setEditingRuangan(null);
        ruanganForm.reset();
        setShowRuanganModal(true);
    };

    const openRuanganEdit = (ruangan) => {
        setEditingRuangan(ruangan);
        ruanganForm.setData({
            nama_ruang: ruangan.nama_ruang || '',
            lokasi_gedung: ruangan.lokasi_gedung || '',
            kategori: ruangan.kategori || 'Medis',
            latitude: ruangan.latitude || '',
            longitude: ruangan.longitude || '',
            foto_file: null
        });
        setShowRuanganModal(true);
    };

    const submitRuangan = (e) => {
        e.preventDefault();
        if (editingRuangan) {
            ruanganForm.post(route('direktori.ruangan.update', editingRuangan.id_ruang), {
                forceFormData: true,
                onSuccess: () => {
                    setShowRuanganModal(false);
                    ruanganForm.reset();
                }
            });
        } else {
            ruanganForm.post(route('direktori.ruangan.store'), {
                forceFormData: true,
                onSuccess: () => {
                    setShowRuanganModal(false);
                    ruanganForm.reset();
                }
            });
        }
    };

    const deleteRuangan = (id) => {
        if (confirm('Yakin ingin menghapus ruangan ini? Pastikan tidak ada aset yang berada di dalam ruangan.')) {
            useForm().post(route('direktori.ruangan.delete', id));
        }
    };

    // Actions: SDM
    const openSdmAdd = () => {
        setEditingSdm(null);
        sdmForm.reset();
        setShowSdmModal(true);
    };

    const openSdmEdit = (sdm) => {
        setEditingSdm(sdm);
        sdmForm.setData({
            username: sdm.username || '',
            email: sdm.email || '',
            password: '',
            nama_lengkap: sdm.nama_lengkap || '',
            role: sdm.role || 'Unit_RS',
            id_ruang: sdm.id_ruang || '',
            nip: sdm.nip || '',
            no_hp: sdm.no_hp || '',
            status: sdm.status || 'Aktif'
        });
        setShowSdmModal(true);
    };

    const submitSdm = (e) => {
        e.preventDefault();
        if (editingSdm) {
            sdmForm.post(route('direktori.sdm.update', editingSdm.id_user), {
                onSuccess: () => {
                    setShowSdmModal(false);
                    sdmForm.reset();
                }
            });
        } else {
            sdmForm.post(route('direktori.sdm.store'), {
                onSuccess: () => {
                    setShowSdmModal(false);
                    sdmForm.reset();
                }
            });
        }
    };

    const deleteSdm = (id) => {
        if (confirm('Yakin ingin menghapus akun staff ini?')) {
            useForm().post(route('direktori.sdm.delete', id));
        }
    };

    // Actions: Kontak
    const openKontakEdit = (sdm) => {
        setEditingKontakSdm(sdm);
        kontakForm.setData({
            kontak_darurat_1: sdm.kontak_darurat_1 || '',
            kontak_darurat_2: sdm.kontak_darurat_2 || '',
            kontak_darurat_3: sdm.kontak_darurat_3 || ''
        });
        setShowKontakModal(true);
    };

    const submitKontak = (e) => {
        e.preventDefault();
        kontakForm.post(route('direktori.kontak.update', editingKontakSdm.id_user), {
            onSuccess: () => {
                setShowKontakModal(false);
                kontakForm.reset();
            }
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-semibold text-2xl text-gray-800 leading-tight">Direktori Unit & SDM</h2>
                        <p className="text-sm text-gray-500 mt-1">Daftar ruangan penempatan aset, data staf, dan kontak darurat rumah sakit</p>
                    </div>
                </div>
            }
        >
            <Head title="Direktori Unit & SDM - MedTrack IPSRS" />

            <div className="py-10">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                    {/* Tabs Navigation */}
                    <div className="bg-white rounded-3xl border border-gray-100 p-6 shadow-sm space-y-6">
                        <div className="border-b border-gray-100 flex space-x-6 text-sm font-semibold">
                            <button
                                onClick={() => setActiveTab('units')}
                                className={`pb-3 transition border-b-2 ${activeTab === 'units' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400 hover:text-gray-600'}`}
                            >
                                📍 Unit & Ruangan ({ruangans.length})
                            </button>
                            <button
                                onClick={() => setActiveTab('sdm')}
                                className={`pb-3 transition border-b-2 ${activeTab === 'sdm' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400 hover:text-gray-600'}`}
                            >
                                👥 SDM & Staf ({sdmList.length})
                            </button>
                            <button
                                onClick={() => setActiveTab('emergency')}
                                className={`pb-3 transition border-b-2 ${activeTab === 'emergency' ? 'border-indigo-600 text-indigo-600' : 'border-transparent text-gray-400 hover:text-gray-600'}`}
                            >
                                📞 Kontak Darurat
                            </button>
                        </div>

                        {/* TAB 1: Units & Rooms */}
                        {activeTab === 'units' && (
                            <div className="space-y-4">
                                {canCrud && (
                                    <button
                                        onClick={openRuanganAdd}
                                        className="px-4 py-2 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl text-xs font-semibold transition shadow-sm"
                                    >
                                        + Tambah Ruangan Baru
                                    </button>
                                )}

                                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    {ruangans.map((ruangan) => (
                                        <div key={ruangan.id_ruang} className="border border-gray-100 rounded-3xl p-5 hover:border-indigo-200 hover:shadow-md transition bg-gray-50/20 flex flex-col justify-between space-y-4">
                                            <div className="space-y-3">
                                                {ruangan.foto ? (
                                                    <img
                                                        src={`/uploads/ruangan/${ruangan.foto}`}
                                                        alt={ruangan.nama_ruang}
                                                        className="w-full h-40 object-cover rounded-2xl border border-gray-200"
                                                    />
                                                ) : (
                                                    <div className="w-full h-40 bg-gray-100 flex items-center justify-center rounded-2xl border border-dashed border-gray-200">
                                                        <span className="text-4xl text-gray-300">🏢</span>
                                                    </div>
                                                )}
                                                <div>
                                                    <h4 className="font-bold text-gray-900 text-base">{ruangan.nama_ruang}</h4>
                                                    <p className="text-xs text-gray-500 mt-1 font-medium">
                                                        🏢 Gedung/Lantai: {ruangan.lokasi_gedung || '-'} • Kategori: {ruangan.kategori}
                                                    </p>
                                                    <span className="mt-3.5 inline-block text-[10px] bg-indigo-50 border border-indigo-150 text-indigo-700 font-bold px-2 py-0.5 rounded-lg">
                                                        Jumlah Aset: {ruangan.aset_count} Unit
                                                    </span>
                                                </div>
                                            </div>

                                            <div className="flex space-x-2 pt-2 border-t border-slate-100">
                                                <button
                                                    onClick={() => openRuanganDetail(ruangan)}
                                                    className="flex-1 py-1.5 bg-indigo-50 border border-indigo-200 hover:bg-indigo-100 text-indigo-700 text-xs font-semibold rounded-lg transition shadow-sm text-center"
                                                >
                                                    Detail
                                                </button>
                                                {canCrud && (
                                                    <>
                                                        <button
                                                            onClick={() => openRuanganEdit(ruangan)}
                                                            className="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg transition shadow-sm"
                                                        >
                                                            Edit
                                                        </button>
                                                        <button
                                                            onClick={() => deleteRuangan(ruangan.id_ruang)}
                                                            className="px-3 py-1.5 bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-200 text-rose-600 text-xs font-semibold rounded-lg transition shadow-sm"
                                                        >
                                                            Hapus
                                                        </button>
                                                    </>
                                                )}
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </div>
                        )}

                        {/* TAB 2: SDM & Staff */}
                        {activeTab === 'sdm' && (
                            <div className="space-y-4">
                                {canCrud && (
                                    <button
                                        onClick={openSdmAdd}
                                        className="px-4 py-2 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl text-xs font-semibold transition shadow-sm"
                                    >
                                        + Tambah Akun Staff
                                    </button>
                                )}

                                <div className="overflow-x-auto rounded-2xl border border-gray-100 shadow-sm">
                                    <table className="w-full text-left border-collapse">
                                        <thead>
                                            <tr className="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                                <th className="px-6 py-4">Nama Lengkap</th>
                                                <th className="px-6 py-4">Username</th>
                                                <th className="px-6 py-4">Email</th>
                                                <th className="px-6 py-4">Role</th>
                                                <th className="px-6 py-4">Unit Ruangan</th>
                                                <th className="px-6 py-4">Status</th>
                                                {canCrud && <th className="px-6 py-4 text-right">Aksi</th>}
                                            </tr>
                                        </thead>
                                        <tbody className="divide-y divide-gray-100 text-sm">
                                            {sdmList.map((sdm) => (
                                                <tr key={sdm.id_user} className="hover:bg-gray-50/50 transition">
                                                    <td className="px-6 py-4 font-semibold text-gray-900">{sdm.nama_lengkap}</td>
                                                    <td className="px-6 py-4 font-mono text-xs text-gray-500">{sdm.username}</td>
                                                    <td className="px-6 py-4 text-gray-600">{sdm.email}</td>
                                                    <td className="px-6 py-4">
                                                        <span className="px-2 py-0.5 bg-indigo-50 border border-indigo-100 text-indigo-700 text-xs font-bold rounded">
                                                            {sdm.role.replace('_', ' ')}
                                                        </span>
                                                    </td>
                                                    <td className="px-6 py-4 text-gray-600 font-medium">{sdm.nama_ruang}</td>
                                                    <td className="px-6 py-4">
                                                        <span className={`px-2.5 py-0.5 text-xs font-bold rounded-full ${sdm.status === 'Aktif' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-gray-100 text-gray-600 border border-gray-200'}`}>
                                                            {sdm.status}
                                                        </span>
                                                    </td>
                                                    {canCrud && (
                                                        <td className="px-6 py-4 text-right whitespace-nowrap space-x-2">
                                                            <button
                                                                onClick={() => openSdmEdit(sdm)}
                                                                className="px-2.5 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg transition shadow-sm"
                                                            >
                                                                Edit
                                                            </button>
                                                            <button
                                                                onClick={() => deleteSdm(sdm.id_user)}
                                                                disabled={sdm.id_user === auth.user.id_user}
                                                                className="px-2.5 py-1.5 bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-200 text-rose-600 text-xs font-semibold rounded-lg transition disabled:opacity-50 shadow-sm"
                                                            >
                                                                Hapus
                                                            </button>
                                                        </td>
                                                    )}
                                                </tr>
                                            ))}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        )}

                        {/* TAB 3: Emergency Contact */}
                        {activeTab === 'emergency' && (
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {sdmList.map((sdm) => (
                                    <div key={sdm.id_user} className="border border-gray-100 rounded-3xl p-5 hover:border-indigo-200 hover:shadow-md transition bg-gray-50/20 flex flex-col justify-between space-y-4">
                                        <div className="space-y-3">
                                            <div>
                                                <h4 className="font-bold text-gray-900 text-base">{sdm.nama_lengkap}</h4>
                                                <p className="text-xs text-indigo-700 mt-1 font-semibold">
                                                    Role: {sdm.role.replace('_', ' ')} • Ruang: {sdm.nama_ruang}
                                                </p>
                                            </div>
                                            <div className="space-y-2 text-xs text-gray-600 bg-white p-3.5 rounded-2xl border border-gray-100">
                                                <p><strong>Kontak 1:</strong> {sdm.kontak_darurat_1 || '-'}</p>
                                                <p><strong>Kontak 2:</strong> {sdm.kontak_darurat_2 || '-'}</p>
                                                <p><strong>Kontak 3:</strong> {sdm.kontak_darurat_3 || '-'}</p>
                                            </div>
                                        </div>

                                        {canCrud && (
                                            <button
                                                onClick={() => openKontakEdit(sdm)}
                                                className="w-full py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg transition text-center shadow-sm"
                                            >
                                                Edit Nomor Kontak Darurat
                                            </button>
                                        )}
                                    </div>
                                ))}
                            </div>
                        )}

                    </div>
                </div>
            </div>

            {/* MODALS */}

            {/* Modal: Ruangan */}
            {showRuanganModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                    <div className="bg-white rounded-3xl w-full max-w-lg p-6 space-y-6 shadow-2xl relative">
                        <div className="flex justify-between items-start border-b border-gray-100 pb-3">
                            <h3 className="font-bold text-lg text-indigo-950">
                                {editingRuangan ? 'Edit Ruangan' : 'Tambah Ruangan Baru'}
                            </h3>
                            <button onClick={() => setShowRuanganModal(false)} className="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                        </div>
                        <form onSubmit={submitRuangan} className="space-y-4 text-sm">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Nama Ruangan *</label>
                                    <input
                                        type="text"
                                        required
                                        value={ruanganForm.data.nama_ruang}
                                        onChange={(e) => ruanganForm.setData('nama_ruang', e.target.value)}
                                        placeholder="Contoh: IGD, ICCU, Ruang OK 1..."
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    />
                                    {ruanganForm.errors.nama_ruang && <p className="text-rose-500 text-xs mt-1">{ruanganForm.errors.nama_ruang}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Kategori Ruangan *</label>
                                    <input
                                        type="text"
                                        required
                                        value={ruanganForm.data.kategori}
                                        onChange={(e) => ruanganForm.setData('kategori', e.target.value)}
                                        placeholder="Contoh: Perawatan, Tindakan, IT, Logistik..."
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Lokasi / Gedung</label>
                                    <input
                                        type="text"
                                        value={ruanganForm.data.lokasi_gedung}
                                        onChange={(e) => ruanganForm.setData('lokasi_gedung', e.target.value)}
                                        placeholder="Contoh: Gedung A Lantai 1..."
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Foto Ruangan (Opsional)</label>
                                    <input
                                        type="file"
                                        onChange={(e) => ruanganForm.setData('foto_file', e.target.files[0])}
                                        className="mt-1.5 w-full text-xs text-gray-500"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Latitude</label>
                                    <input
                                        type="number"
                                        step="any"
                                        value={ruanganForm.data.latitude}
                                        onChange={(e) => ruanganForm.setData('latitude', e.target.value)}
                                        placeholder="Cth: -6.985"
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Longitude</label>
                                    <input
                                        type="number"
                                        step="any"
                                        value={ruanganForm.data.longitude}
                                        onChange={(e) => ruanganForm.setData('longitude', e.target.value)}
                                        placeholder="Cth: 108.485"
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                    />
                                </div>
                                <div className="col-span-1 md:col-span-2 space-y-1.5">
                                    <label className="block text-xs font-semibold text-gray-500 uppercase tracking-wider">Pilih Lokasi Ruangan (Klik pada peta satelit)</label>
                                    <div id="picker-map-container" className="w-full h-56 rounded-2xl border border-gray-200 overflow-hidden relative z-0">
                                        {!leafletLoaded && (
                                            <div className="w-full h-full bg-gray-50 flex flex-col items-center justify-center text-gray-400">
                                                <div className="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin mb-2"></div>
                                                <span className="text-xs">Memuat peta...</span>
                                            </div>
                                        )}
                                    </div>
                                </div>
                            </div>
                            <div className="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                                <button
                                    type="button"
                                    onClick={() => setShowRuanganModal(false)}
                                    className="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-semibold text-gray-600"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={ruanganForm.processing}
                                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl font-semibold shadow-sm"
                                >
                                    Simpan Ruangan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal: SDM */}
            {showSdmModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                    <div className="bg-white rounded-3xl w-full max-w-lg p-6 space-y-6 shadow-2xl relative">
                        <div className="flex justify-between items-start border-b border-gray-100 pb-3">
                            <h3 className="font-bold text-lg text-indigo-950">
                                {editingSdm ? 'Edit Akun Staff' : 'Tambah Akun Staff Baru'}
                            </h3>
                            <button onClick={() => setShowSdmModal(false)} className="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                        </div>
                        <form onSubmit={submitSdm} className="space-y-4 text-sm">
                            <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Nama Lengkap *</label>
                                    <input
                                        type="text"
                                        required
                                        value={sdmForm.data.nama_lengkap}
                                        onChange={(e) => sdmForm.setData('nama_lengkap', e.target.value)}
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">NIP (Opsional)</label>
                                    <input
                                        type="text"
                                        value={sdmForm.data.nip}
                                        onChange={(e) => sdmForm.setData('nip', e.target.value)}
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Username *</label>
                                    <input
                                        type="text"
                                        required
                                        value={sdmForm.data.username}
                                        onChange={(e) => sdmForm.setData('username', e.target.value)}
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                    />
                                    {sdmForm.errors.username && <p className="text-rose-500 text-xs mt-1">{sdmForm.errors.username}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Email *</label>
                                    <input
                                        type="email"
                                        required
                                        value={sdmForm.data.email}
                                        onChange={(e) => sdmForm.setData('email', e.target.value)}
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                    />
                                    {sdmForm.errors.email && <p className="text-rose-500 text-xs mt-1">{sdmForm.errors.email}</p>}
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">
                                        {editingSdm ? 'Password Baru (Kosongkan jika tidak ganti)' : 'Password *'}
                                    </label>
                                    <input
                                        type="password"
                                        required={!editingSdm}
                                        value={sdmForm.data.password}
                                        onChange={(e) => sdmForm.setData('password', e.target.value)}
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">No. HP / WhatsApp</label>
                                    <input
                                        type="text"
                                        value={sdmForm.data.no_hp}
                                        onChange={(e) => sdmForm.setData('no_hp', e.target.value)}
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                    />
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Role Hak Akses *</label>
                                    <select
                                        value={sdmForm.data.role}
                                        onChange={(e) => sdmForm.setData('role', e.target.value)}
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                    >
                                        <option value="Admin_IPSRS">Admin IPSRS</option>
                                        <option value="Staf_IPSRS">Staf IPSRS / Teknisi</option>
                                        <option value="Staf_Logistik">Staf Logistik</option>
                                        <option value="Unit_RS">Staf Unit RS</option>
                                        <option value="Kepala_IPSRS">Kepala IPSRS</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Unit Ruangan Penempatan</label>
                                    <select
                                        value={sdmForm.data.id_ruang}
                                        onChange={(e) => sdmForm.setData('id_ruang', e.target.value)}
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                    >
                                        <option value="">-- Tidak Ada / Umum --</option>
                                        {ruangans.map((r) => (
                                            <option key={r.id_ruang} value={r.id_ruang}>{r.nama_ruang}</option>
                                        ))}
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Status Akun *</label>
                                    <select
                                        value={sdmForm.data.status}
                                        onChange={(e) => sdmForm.setData('status', e.target.value)}
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                    >
                                        <option value="Aktif">Aktif</option>
                                        <option value="Nonaktif">Nonaktif</option>
                                    </select>
                                </div>
                            </div>
                            <div className="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                                <button
                                    type="button"
                                    onClick={() => setShowSdmModal(false)}
                                    className="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-semibold text-gray-600"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={sdmForm.processing}
                                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl font-semibold shadow-sm"
                                >
                                    Simpan Staff
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal: Kontak Darurat */}
            {showKontakModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                    <div className="bg-white rounded-3xl w-full max-w-md p-6 space-y-6 shadow-2xl relative">
                        <div className="flex justify-between items-start border-b border-gray-100 pb-3">
                            <h3 className="font-bold text-lg text-indigo-950">
                                Edit Kontak Darurat
                            </h3>
                            <button onClick={() => setShowKontakModal(false)} className="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                        </div>
                        <form onSubmit={submitKontak} className="space-y-4 text-sm">
                            <p className="text-xs text-gray-500">
                                Masukkan nomor telepon kontak darurat yang dapat dihubungi untuk {editingKontakSdm?.nama_lengkap}.
                            </p>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Kontak Darurat 1</label>
                                <input
                                    type="text"
                                    value={kontakForm.data.kontak_darurat_1}
                                    onChange={(e) => kontakForm.setData('kontak_darurat_1', e.target.value)}
                                    placeholder="Contoh: 08123456789 (Istri)"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Kontak Darurat 2</label>
                                <input
                                    type="text"
                                    value={kontakForm.data.kontak_darurat_2}
                                    onChange={(e) => kontakForm.setData('kontak_darurat_2', e.target.value)}
                                    placeholder="Contoh: 08123456780 (Suami)"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                />
                            </div>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Kontak Darurat 3</label>
                                <input
                                    type="text"
                                    value={kontakForm.data.kontak_darurat_3}
                                    onChange={(e) => kontakForm.setData('kontak_darurat_3', e.target.value)}
                                    placeholder="Contoh: 08123456781 (Saudara)"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                />
                            </div>
                            <div className="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                                <button
                                    type="button"
                                    onClick={() => setShowKontakModal(false)}
                                    className="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-250 rounded-xl font-semibold text-gray-600"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={kontakForm.processing}
                                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl font-semibold shadow-sm"
                                >
                                    Simpan Kontak
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal: Detail Ruangan */}
            {showDetailRuanganModal && selectedDetailRuangan && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                    <div className="bg-white rounded-3xl w-full max-w-4xl p-6 space-y-6 shadow-2xl relative">
                        <div className="flex justify-between items-start border-b border-gray-100 pb-3">
                            <div>
                                <h3 className="font-bold text-lg text-indigo-950">Detail Ruangan / Unit</h3>
                                <p className="text-xs text-gray-500 mt-0.5">Informasi lokasi, aset terdaftar, dan pemantauan satelit</p>
                            </div>
                            <button onClick={() => setShowDetailRuanganModal(false)} className="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                        </div>

                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {/* Left Side: Info & Assets */}
                            <div className="space-y-4">
                                <div className="flex gap-4">
                                    <div className="w-24 h-24 shrink-0 rounded-2xl overflow-hidden border border-gray-200 bg-gray-50">
                                        {selectedDetailRuangan.foto ? (
                                            <img
                                                src={`/uploads/ruangan/${selectedDetailRuangan.foto}`}
                                                alt={selectedDetailRuangan.nama_ruang}
                                                className="w-full h-full object-cover"
                                            />
                                        ) : (
                                            <div className="w-full h-full flex items-center justify-center text-3xl">
                                                🏢
                                            </div>
                                        )}
                                    </div>
                                    <div className="space-y-1">
                                        <h4 className="font-bold text-gray-900 text-lg">{selectedDetailRuangan.nama_ruang}</h4>
                                        <p className="text-sm text-gray-500 font-medium">🏢 Gedung: {selectedDetailRuangan.lokasi_gedung || '-'}</p>
                                        <p className="text-xs text-indigo-700 font-semibold bg-indigo-50 border border-indigo-100 px-2 py-0.5 rounded-lg inline-block">
                                            Kategori: {selectedDetailRuangan.kategori}
                                        </p>
                                    </div>
                                </div>

                                <div className="border-t border-gray-100 pt-4 space-y-3">
                                    <h5 className="font-bold text-sm text-gray-800">Daftar Aset Terdaftar ({selectedDetailRuangan.aset_count || 0})</h5>
                                    <div className="max-h-60 overflow-y-auto space-y-2 border border-gray-100 rounded-2xl p-2.5 bg-gray-50/55">
                                        {selectedDetailRuangan.asets_list && selectedDetailRuangan.asets_list.length > 0 ? (
                                            selectedDetailRuangan.asets_list.map((a) => (
                                                <a
                                                    key={a.id_aset}
                                                    href={`/aset/${a.id_aset}`}
                                                    target="_blank"
                                                    rel="noopener noreferrer"
                                                    className="p-2.5 bg-white border border-gray-100 rounded-xl hover:border-indigo-200 hover:shadow-sm transition flex justify-between items-center"
                                                >
                                                    <div>
                                                        <span className="text-[10px] font-mono text-indigo-600 font-semibold block">{a.kode_label}</span>
                                                        <span className="text-xs font-bold text-gray-800 block mt-0.5">{a.nama_alat}</span>
                                                    </div>
                                                    <span className={`px-2 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider ${
                                                        a.status_kondisi === 'Baik' ? 'bg-emerald-50 text-emerald-700 border border-emerald-100' : 'bg-rose-50 text-rose-700 border border-rose-100'
                                                    }`}>
                                                        {a.status_kondisi}
                                                    </span>
                                                </a>
                                            ))
                                        ) : (
                                            <p className="text-center py-8 text-xs text-gray-400 font-semibold italic">Tidak ada aset terdaftar di ruangan ini.</p>
                                        )}
                                    </div>
                                </div>
                            </div>

                            {/* Right Side: Satellite Map */}
                            <div className="space-y-3 flex flex-col justify-between">
                                <div>
                                    <h5 className="font-bold text-sm text-gray-800">Koordinat Geolocation (Peta Satelit)</h5>
                                    <p className="text-xs text-gray-400 mt-0.5">
                                        {selectedDetailRuangan.latitude && selectedDetailRuangan.longitude
                                            ? `Lat: ${selectedDetailRuangan.latitude}, Lng: ${selectedDetailRuangan.longitude}`
                                            : 'Lokasi koordinat belum ditentukan.'
                                        }
                                    </p>
                                </div>

                                <div className="border border-gray-200 rounded-2xl overflow-hidden shadow-inner relative z-0 flex-1 min-h-[250px]">
                                    {selectedDetailRuangan.latitude && selectedDetailRuangan.longitude ? (
                                        <div id="detail-room-map" className="w-full h-full bg-gray-50 flex items-center justify-center text-gray-400">
                                            {!leafletLoaded && (
                                                <div className="flex flex-col items-center space-y-2">
                                                    <div className="w-6 h-6 border-2 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                                                    <span className="text-xs">Memuat peta...</span>
                                                </div>
                                            )}
                                        </div>
                                    ) : (
                                        <div className="w-full h-full flex flex-col items-center justify-center text-gray-400 bg-gray-50 border border-dashed border-gray-200 p-6 text-center">
                                            <span className="text-4xl mb-2">📍</span>
                                            <span className="text-xs font-semibold">Titik koordinat ruangan belum diinput.</span>
                                            {canCrud && (
                                                <button
                                                    onClick={() => {
                                                        setShowDetailRuanganModal(false);
                                                        openRuanganEdit(selectedDetailRuangan);
                                                    }}
                                                    className="mt-3 px-3 py-1.5 bg-indigo-600 text-white rounded-lg text-xs font-semibold hover:bg-indigo-750 transition"
                                                >
                                                    Input Koordinat Sekarang
                                                </button>
                                            )}
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>

                        <div className="flex justify-end pt-4 border-t border-gray-100">
                            <button
                                type="button"
                                onClick={() => setShowDetailRuanganModal(false)}
                                className="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-semibold text-gray-600 text-xs shadow-sm"
                            >
                                Tutup
                            </button>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
