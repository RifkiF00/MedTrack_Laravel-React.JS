import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';
import { useEffect, useRef } from 'react';

export default function Dashboard({ auth, role, nama, stats, chart, dashboardNotifications = [], dashboardNotificationCount = 0 }) {
    const isUnit = role === 'Unit_RS';
    const perluPenanganan = (stats.rusak_ringan || 0) + (stats.rusak_berat || 0) + (stats.maintenance || 0);
    const persenBaik = stats.total_aset > 0 ? Math.round(((stats.baik || 0) / stats.total_aset) * 100) : 0;
    const persenPenanganan = stats.total_aset > 0 ? Math.round((perluPenanganan / stats.total_aset) * 100) : 0;

    // Refs for chart elements
    const chartKondisiRef = useRef(null);
    const chartKategoriRef = useRef(null);
    const chartOperasionalRef = useRef(null);
    const chartStatusBarRef = useRef(null);

    // Instances of active charts to clear on unmount
    const chartInstances = useRef([]);

    useEffect(() => {
        // Load Chart.js from CDN dynamically
        const script = document.createElement('script');
        script.src = 'https://cdn.jsdelivr.net/npm/chart.js';
        script.async = true;
        script.onload = () => {
            if (window.Chart) {
                initializeCharts();
            }
        };
        document.body.appendChild(script);

        return () => {
            // Destroy all active charts on unmount to prevent canvas reuse errors
            chartInstances.current.forEach(instance => {
                if (instance && typeof instance.destroy === 'function') {
                    instance.destroy();
                }
            });
            document.body.removeChild(script);
        };
    }, [stats, chart]);

    const initializeCharts = () => {
        const Chart = window.Chart;

        // Destroy previous instances if any (e.g. on hot-reload)
        chartInstances.current.forEach(instance => {
            if (instance && typeof instance.destroy === 'function') {
                instance.destroy();
            }
        });
        chartInstances.current = [];

        // 1. Chart Kondisi (Doughnut)
        if (chartKondisiRef.current) {
            const ctx = chartKondisiRef.current.getContext('2d');
            const c = new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Baik', 'Rusak Ringan', 'Rusak Berat', 'Maintenance', 'Gudang'],
                    datasets: [{
                        data: [
                            stats.baik || 0,
                            stats.rusak_ringan || 0,
                            stats.rusak_berat || 0,
                            stats.maintenance || 0,
                            stats.gudang || 0
                        ],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444', '#0ea5e9', '#64748b'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 } }
                        }
                    }
                }
            });
            chartInstances.current.push(c);
        }

        // 2. Chart Kategori (Pie)
        if (chartKategoriRef.current) {
            const ctx = chartKategoriRef.current.getContext('2d');
            const c = new Chart(ctx, {
                type: 'pie',
                data: {
                    labels: ['Medis', 'Sarpras', 'IT'],
                    datasets: [{
                        data: [
                            stats.total_medis || 0,
                            stats.total_sarpras || 0,
                            stats.total_it || 0
                        ],
                        backgroundColor: ['#0a3a60', '#f97316', '#8b5cf6'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { boxWidth: 12, font: { size: 11 } }
                        }
                    }
                }
            });
            chartInstances.current.push(c);
        }

        // 3. Chart Operasional vs Penanganan (Bar)
        if (chartOperasionalRef.current) {
            const ctx = chartOperasionalRef.current.getContext('2d');
            const c = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: ['Operasional', 'Perlu Penanganan', 'Gudang'],
                    datasets: [{
                        label: 'Jumlah Aset',
                        data: [
                            stats.baik || 0,
                            perluPenanganan,
                            stats.gudang || 0
                        ],
                        backgroundColor: ['#0a3a60', '#ef4444', '#64748b'],
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
            chartInstances.current.push(c);
        }

        // 4. Aset per Ruangan (Line)
        if (chartStatusBarRef.current) {
            const ctx = chartStatusBarRef.current.getContext('2d');
            const c = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: chart.labels || [],
                    datasets: [{
                        label: 'Jumlah Aset',
                        data: (chart.datasets && chart.datasets[0]) ? chart.datasets[0].data : [],
                        borderColor: '#0a3a60',
                        backgroundColor: 'rgba(10, 58, 96, 0.08)',
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            });
            chartInstances.current.push(c);
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-extrabold text-2xl text-slate-800 leading-tight">Dashboard Overview</h2>
                        <p className="text-xs text-slate-500 mt-1">Sistem Tracking & Pemeliharaan Aset Medis IPSRS</p>
                    </div>
                </div>
            }
        >
            <Head title="Dashboard Overview" />

            <div className="space-y-6 pb-8">
                {/* Welcome Banner Card (matching user's screenshot layout) */}
                <div className="bg-[#0a3a60] rounded-[2rem] p-8 text-white shadow-lg relative overflow-hidden">
                    <div className="absolute right-0 top-0 opacity-10 translate-x-12 -translate-y-12 select-none pointer-events-none">
                        <svg className="w-96 h-96" fill="currentColor" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="40" />
                        </svg>
                    </div>
                    <div className="relative z-10 flex flex-col md:flex-row md:justify-between md:items-center gap-6">
                        <div className="space-y-4">
                            <span className="inline-block px-3 py-1 bg-white/10 border border-white/20 text-sky-200 text-xs font-bold rounded-full uppercase tracking-wider">
                                System Active
                            </span>
                            <div className="space-y-1">
                                <h3 className="text-3xl font-extrabold tracking-tight">Selamat Datang, {nama}!</h3>
                                <p className="text-sky-100 max-w-2xl text-sm font-light leading-relaxed">
                                    Kelola mutasi, pengadaan, dan pemeliharaan aset medis rumah sakit secara realtime dengan tertib administrasi.
                                </p>
                            </div>
                        </div>

                        {/* Peran / Role Badge */}
                        <div className="bg-white/10 backdrop-blur-md px-6 py-3 rounded-2xl border border-white/10 shrink-0 min-w-[150px] text-center">
                            <div className="text-xs text-sky-200 uppercase tracking-widest font-semibold">Peran</div>
                            <div className="text-lg font-bold text-white mt-1 capitalize">{role.replace('_', ' ')}</div>
                        </div>
                    </div>
                </div>

                {/* 4 Stats Cards Grid (matching screenshot layout) */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {/* Card 1: Total Ruangan */}
                    <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                        <div className="flex justify-between items-start">
                            <div>
                                <span className="text-[10px] font-bold text-slate-400 tracking-wider uppercase block">Total Ruangan</span>
                                <span className="text-3xl font-extrabold text-slate-800 mt-1 block">{stats.total_ruangan || 0}</span>
                            </div>
                            <div className="w-10 h-10 bg-sky-50 rounded-xl flex items-center justify-center">
                                <svg className="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                            </div>
                        </div>
                        <div className="mt-4 pt-3 border-t border-slate-50 flex items-center text-xs text-emerald-600 font-semibold">
                            <span className="mr-1">↑</span> Aktif & Terdaftar
                        </div>
                    </div>

                    {/* Card 2: Total Barang / Aset */}
                    <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                        <div className="flex justify-between items-start">
                            <div>
                                <span className="text-[10px] font-bold text-slate-400 tracking-wider uppercase block">Total Aset Medis</span>
                                <span className="text-3xl font-extrabold text-slate-800 mt-1 block">{stats.total_aset || 0}</span>
                            </div>
                            <div className="w-10 h-10 bg-sky-50 rounded-xl flex items-center justify-center">
                                <svg className="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                                </svg>
                            </div>
                        </div>
                        <div className="mt-4 pt-3 border-t border-slate-50 flex items-center text-xs text-emerald-600 font-semibold">
                            <span className="mr-1">↑</span> Stok Terdata
                        </div>
                    </div>

                    {/* Card 3: Total Mutasi */}
                    <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                        <div className="flex justify-between items-start">
                            <div>
                                <span className="text-[10px] font-bold text-slate-400 tracking-wider uppercase block">Total Mutasi</span>
                                <span className="text-3xl font-extrabold text-slate-800 mt-1 block">{stats.total_mutasi || 0}</span>
                            </div>
                            <div className="w-10 h-10 bg-sky-50 rounded-xl flex items-center justify-center">
                                <svg className="h-5 w-5 text-sky-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                                </svg>
                            </div>
                        </div>
                        <div className="mt-4 pt-3 border-t border-slate-50 flex items-center text-xs text-sky-600 font-semibold">
                            <span className="mr-1">⇄</span> Perpindahan Log
                        </div>
                    </div>

                    {/* Card 4: Work Order Pending */}
                    <div className="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 flex flex-col justify-between">
                        <div className="flex justify-between items-start">
                            <div>
                                <span className="text-[10px] font-bold text-slate-400 tracking-wider uppercase block">WO Pending</span>
                                <span className="text-3xl font-extrabold text-slate-800 mt-1 block">{stats.total_wo_pending || 0}</span>
                            </div>
                            <div className="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center">
                                <svg className="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" strokeWidth="2">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                </svg>
                            </div>
                        </div>
                        <div className="mt-4 pt-3 border-t border-slate-50 flex items-center text-xs text-amber-600 font-semibold">
                            <span className="mr-1">⚠</span> Butuh Verifikasi
                        </div>
                    </div>
                </div>

                {/* Progress Indeks Kelayakan & Counters Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {/* Progress Indeks */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 space-y-6 flex flex-col justify-center">
                        <h3 className="font-bold text-lg text-gray-900 leading-tight">
                            {isUnit ? 'Indeks Kelayakan Aset Unit' : 'Indeks Kelayakan Aset'}
                        </h3>

                        <div className="space-y-4">
                            <div>
                                <div className="flex justify-between text-sm text-gray-700 font-semibold mb-2">
                                    <span>Laik Pakai (Baik)</span>
                                    <span>{stats.baik} dari {stats.total_aset} ({persenBaik}%)</span>
                                </div>
                                <div className="bg-gray-100 h-2.5 rounded-full overflow-hidden">
                                    <div className="bg-emerald-500 h-2.5 rounded-full transition-all duration-500" style={{ width: `${persenBaik}%` }}></div>
                                </div>
                            </div>

                            <div>
                                <div className="flex justify-between text-sm text-gray-700 font-semibold mb-2">
                                    <span>Perlu Penanganan</span>
                                    <span>{perluPenanganan} dari {stats.total_aset} ({persenPenanganan}%)</span>
                                </div>
                                <div className="bg-gray-100 h-2.5 rounded-full overflow-hidden">
                                    <div className="bg-rose-500 h-2.5 rounded-full transition-all duration-500" style={{ width: `${persenPenanganan}%` }}></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Summary Kondisi Counters */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                        <h3 className="font-bold text-lg text-gray-900 mb-4">
                            {isUnit ? 'Ringkasan Kondisi Unit' : 'Ringkasan Kondisi'}
                        </h3>
                        <div className="grid grid-cols-2 md:grid-cols-3 gap-4">
                            <div className="bg-emerald-50 border border-emerald-100/50 p-4 rounded-2xl">
                                <div className="text-xs font-semibold text-emerald-800 uppercase tracking-wide">Baik</div>
                                <div className="text-2xl font-bold text-emerald-700 mt-2">{stats.baik}</div>
                            </div>

                            <div className="bg-gray-50 border border-gray-100 p-4 rounded-2xl">
                                <div className="text-xs font-semibold text-gray-800 uppercase tracking-wide">Gudang</div>
                                <div className="text-2xl font-bold text-gray-700 mt-2">{stats.gudang}</div>
                            </div>

                            <div className="bg-amber-50 border border-amber-100/50 p-4 rounded-2xl">
                                <div className="text-xs font-semibold text-amber-800 uppercase tracking-wide">Rusak Ringan</div>
                                <div className="text-2xl font-bold text-amber-700 mt-2">{stats.rusak_ringan}</div>
                            </div>

                            <div className="bg-rose-50 border border-rose-100/50 p-4 rounded-2xl">
                                <div className="text-xs font-semibold text-rose-800 uppercase tracking-wide">Rusak Berat</div>
                                <div className="text-2xl font-bold text-rose-700 mt-2">{stats.rusak_berat}</div>
                            </div>

                            <div className="bg-sky-50 border border-sky-100/50 p-4 rounded-2xl md:col-span-2">
                                <div className="text-xs font-semibold text-sky-800 uppercase tracking-wide">Maintenance</div>
                                <div className="text-2xl font-bold text-sky-700 mt-2">{stats.maintenance}</div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Charts Grid */}
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {/* 1. Kondisi Aset Chart */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                        <h3 className="font-bold text-lg text-gray-900 mb-4">{isUnit ? 'Kondisi Aset Unit' : 'Kondisi Aset'}</h3>
                        <div className="relative h-[250px] w-full flex-1">
                            <canvas ref={chartKondisiRef}></canvas>
                        </div>
                    </div>

                    {/* 2. Komposisi Kategori Chart */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                        <h3 className="font-bold text-lg text-gray-900 mb-4">{isUnit ? 'Komposisi Kategori Unit' : 'Komposisi Kategori'}</h3>
                        <div className="relative h-[250px] w-full flex-1">
                            <canvas ref={chartKategoriRef}></canvas>
                        </div>
                    </div>

                    {/* 3. Operasional vs Penanganan Chart */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                        <h3 className="font-bold text-lg text-gray-900 mb-4">{isUnit ? 'Operasional vs Penanganan Unit' : 'Operasional vs Penanganan'}</h3>
                        <div className="relative h-[250px] w-full flex-1">
                            <canvas ref={chartOperasionalRef}></canvas>
                        </div>
                    </div>

                    {/* 4. Aset per Ruangan / Bar Chart */}
                    <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6 flex flex-col">
                        <h3 className="font-bold text-lg text-gray-900 mb-4">{isUnit ? 'Aset pada Unit Anda' : 'Aset per Ruangan'}</h3>
                        <div className="relative h-[250px] w-full flex-1">
                            <canvas ref={chartStatusBarRef}></canvas>
                        </div>
                    </div>
                </div>

                {/* Notifications Panel */}
                <div className="bg-white rounded-2xl border border-slate-100 shadow-sm p-6">
                    <div className="flex justify-between items-center border-b border-slate-100 pb-3 mb-4">
                        <h4 className="font-semibold text-lg text-gray-900">Notifikasi Sistem</h4>
                        <span className="px-2.5 py-0.5 bg-rose-100 text-rose-700 text-xs font-bold rounded-full">
                            {dashboardNotificationCount} Notifikasi
                        </span>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                        {dashboardNotifications.length > 0 ? (
                            dashboardNotifications.map((notif) => (
                                <div 
                                    key={notif.id}
                                    className="flex items-start space-x-3.5 p-4 bg-slate-50 rounded-2xl hover:bg-slate-100 transition duration-150 border border-slate-100"
                                >
                                    <span className="text-xl leading-none mt-0.5">{notif.icon}</span>
                                    <div>
                                        <p className="text-sm font-bold text-gray-900">{notif.title}</p>
                                        <p className="text-xs text-gray-600 mt-1 leading-relaxed">{notif.message}</p>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <div className="col-span-2 flex flex-col items-center justify-center py-12 text-gray-400 space-y-2">
                                <svg className="w-12 h-12 stroke-current" fill="none" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                                </svg>
                                <p className="text-sm font-semibold">Tidak ada notifikasi baru</p>
                            </div>
                        )}
                    </div>
                </div>

            </div>
        </AuthenticatedLayout>
    );
}
