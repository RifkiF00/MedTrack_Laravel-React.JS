import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Map({ auth, asets, ruangans = [] }) {
    const [leafletLoaded, setLeafletLoaded] = useState(false);

    useEffect(() => {
        // 1. Inject Leaflet CSS ke Head
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = 'https://unpkg.com/leaflet@1.9.4/dist/leaflet.css';
        link.id = 'leaflet-css';
        if (!document.getElementById('leaflet-css')) {
            document.head.appendChild(link);
        }

        // 2. Inject custom CSS rules to prevent Leaflet white borders/bg on divIcons
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

        // 3. Inject Leaflet JS ke Body
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

        // Cleanup saat unmount
        return () => {
            const css = document.getElementById('leaflet-css');
            const js = document.getElementById('leaflet-js');
            const customStyles = document.getElementById('leaflet-custom-styles');
            if (css) css.remove();
            if (js) js.remove();
            if (customStyles) customStyles.remove();
        };
    }, []);

    useEffect(() => {
        if (!leafletLoaded || !window.L) return;
        if (asets.length === 0 && ruangans.length === 0) return;

        const L = window.L;

        // Satellite layer (Default)
        const satelliteLayer = L.tileLayer('https://server.arcgisonline.com/ArcGIS/rest/services/World_Imagery/MapServer/tile/{z}/{y}/{x}', {
            attribution: 'Tiles &copy; Esri &mdash; Source: Esri, i-cubed, USDA, USGS, AEX, GeoEye, Getmapping, Aerogrid, IGN, IGP, UPR-EGP, and the GIS User Community'
        });

        // Streets layer (OpenStreetMap)
        const streetsLayer = L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        });

        // Koordinat default (Tengah Jakarta / RS Pusat)
        const defaultLat = asets[0]?.latitude || ruangans[0]?.latitude || -6.2088;
        const defaultLng = asets[0]?.longitude || ruangans[0]?.longitude || 106.8456;

        // Inisialisasi Map dengan default Satellite layer
        const map = L.map('leaflet-map-container', {
            center: [defaultLat, defaultLng],
            zoom: 16,
            layers: [satelliteLayer]
        });

        // Add Layer Switch Control
        const baseMaps = {
            "Satelit (Citra Bumi)": satelliteLayer,
            "Jalan Standar": streetsLayer
        };
        L.control.layers(baseMaps).addTo(map);

        // Blue/Indigo Marker for Aset
        const blueIcon = L.divIcon({
            html: `
                <div class="relative flex items-center justify-center">
                    <span class="absolute inline-flex h-6 w-6 rounded-full bg-indigo-400 opacity-75 animate-ping"></span>
                    <svg class="w-8 h-8 text-indigo-600 relative z-10" style="filter: drop-shadow(0px 2px 4px rgba(0,0,0,0.35));" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/>
                    </svg>
                </div>
            `,
            className: 'custom-div-icon',
            iconSize: [32, 32],
            iconAnchor: [16, 32],
            popupAnchor: [0, -32]
        });

        // Red/Rose Marker for Ruangan
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

        // Tambahkan Marker untuk setiap Ruangan
        ruangans.forEach((ruangan) => {
            let assetsHtml = '';
            if (ruangan.asets_list && ruangan.asets_list.length > 0) {
                assetsHtml = `
                    <div style="margin-top: 8px; max-height: 150px; overflow-y: auto; border-top: 1px solid #e5e7eb; padding-top: 8px;">
                        <p style="margin: 0 0 4px 0; font-size: 11px; font-weight: 700; color: #374151;">Daftar Aset (${ruangan.total_aset}):</p>
                        <ul style="margin: 0; padding: 0 0 0 16px; font-size: 11px; color: #4b5563; line-height: 1.4;">
                            ${ruangan.asets_list.map(aset => `
                                <li style="margin-bottom: 4px;">
                                    <a href="/aset/${aset.id_aset}" target="_blank" style="color: #4f46e5; text-decoration: underline; font-weight: 600;">
                                        ${aset.nama_alat}
                                    </a>
                                    <span style="font-size: 9px; background: ${aset.status_kondisi === 'Baik' ? '#d1fae5; color: #065f46' : '#fee2e2; color: #991b1b'}; padding: 1px 3px; border-radius: 3px; margin-left: 4px; display: inline-block;">
                                        ${aset.status_kondisi}
                                    </span>
                                </li>
                            `).join('')}
                        </ul>
                    </div>
                `;
            } else {
                assetsHtml = `
                    <p style="margin: 8px 0 0 0; font-size: 11px; color: #9ca3af; font-style: italic;">Tidak ada aset di ruangan ini.</p>
                `;
            }

            const popupContent = `
                <div style="font-family: ui-sans-serif, system-ui, sans-serif; padding: 4px; min-width: 200px;">
                    <span style="font-size: 10px; background: #ffe4e6; color: #e11d48; padding: 2px 6px; border-radius: 9999px; font-weight: 700; display: inline-block; margin-bottom: 6px;">
                        🚪 Ruangan / Room
                    </span>
                    <h5 style="margin: 0 0 4px 0; font-weight: 700; font-size: 14px; color: #9f1239;">${ruangan.nama_ruang}</h5>
                    <div style="font-size: 11px; color: #4b5563; line-height: 1.4;">
                        <p style="margin: 2px 0;">🏢 <b>Gedung:</b> ${ruangan.lokasi_gedung || '-'}</p>
                        <p style="margin: 2px 0;">🏷️ <b>Kategori:</b> ${ruangan.kategori || '-'}</p>
                    </div>
                    ${assetsHtml}
                </div>
            `;

            L.marker([ruangan.latitude, ruangan.longitude], { icon: redIcon })
                .addTo(map)
                .bindPopup(popupContent);
        });

        // Cleanup map instance saat component re-render / unmount
        return () => {
            map.remove();
        };

    }, [leafletLoaded, asets, ruangans]);

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="font-semibold text-xl text-gray-800 leading-tight">GPS Geolocation Tracking Map</h2>
                    <p className="text-sm text-gray-500 mt-1">Pemantauan persebaran lokasi real-time seluruh aset medis di rumah sakit</p>
                </div>
            }
        >
            <Head title="Peta Tracking Aset" />

            <div className="py-10">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden p-6 space-y-4">
                        <div className="flex justify-between items-center">
                            <span className="text-sm text-gray-600 font-semibold flex items-center space-x-2">
                                <span className="w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse"></span>
                                <span>Menampilkan {ruangans.length} ruangan dengan log koordinat GPS aktif</span>
                            </span>
                        </div>

                        {/* Map Container */}
                        <div className="border border-gray-250 rounded-2xl overflow-hidden shadow-inner relative z-0">
                            <div 
                                id="leaflet-map-container" 
                                className="w-full h-[550px] bg-gray-50 flex items-center justify-center text-gray-400"
                            >
                                {!leafletLoaded && (
                                    <div className="flex flex-col items-center space-y-3">
                                        <div className="w-8 h-8 border-4 border-indigo-600 border-t-transparent rounded-full animate-spin"></div>
                                        <p className="text-sm font-medium">Memuat peta GPS...</p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
