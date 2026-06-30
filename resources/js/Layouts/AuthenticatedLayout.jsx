import { useState, useEffect } from 'react';
import Dropdown from '@/Components/Dropdown';
import { Link, usePage, router } from '@inertiajs/react';

export default function Authenticated({ user, header, children }) {
    const [showingNavigationDropdown, setShowingNavigationDropdown] = useState(false);
    const { upcomingEvents, upcomingCalibrations, notifications } = usePage().props;

    // Dismissed notifications state (saved in localStorage)
    const [dismissedNotifs, setDismissedNotifs] = useState(() => {
        try {
            return JSON.parse(localStorage.getItem('dismissed_notifs') || '[]');
        } catch (e) {
            return [];
        }
    });

    const handleNotifClick = (notifId, notifText) => {
        const key = notifId + '_' + notifText;
        const updated = [...dismissedNotifs, key];
        setDismissedNotifs(updated);
        localStorage.setItem('dismissed_notifs', JSON.stringify(updated));
    };

    const activeNotifications = notifications?.filter(notif => {
        const key = notif.id + '_' + notif.text;
        return !dismissedNotifs.includes(key);
    }) || [];

    // States for search and calendar
    const [searchText, setSearchText] = useState('');
    const [startDate, setStartDate] = useState(new Date());
    const [selectedDate, setSelectedDate] = useState(new Date());

    // QR Code scanner states
    const [isScanModalOpen, setIsScanModalOpen] = useState(false);
    const [scannerLibLoaded, setScannerLibLoaded] = useState(false);
    const [activeScanTab, setActiveScanTab] = useState('camera'); // 'camera' or 'file'
    const [scanError, setScanError] = useState('');
    const [cameras, setCameras] = useState([]);
    const [selectedCameraId, setSelectedCameraId] = useState('');

    // Dynamic load of html5-qrcode library
    useEffect(() => {
        if (!isScanModalOpen) return;

        const scriptId = 'html5-qrcode-cdn';
        let script = document.getElementById(scriptId);

        if (!script) {
            script = document.createElement('script');
            script.src = 'https://unpkg.com/html5-qrcode';
            script.id = scriptId;
            script.async = true;
            script.onload = () => setScannerLibLoaded(true);
            document.body.appendChild(script);
        } else {
            setScannerLibLoaded(true);
        }
    }, [isScanModalOpen]);

    const handleDecodedQR = (decodedText) => {
        const query = decodedText.trim();
        // Cek jika yang di-scan adalah URL Detail Aset (mendukung versi Native dan Laravel)
        if (query.includes('/aset/')) {
            const match = query.match(/\/aset\/(?:detail\/|show\/)?(\d+)/i) || query.match(/(\d+)$/);
            if (match && match[1]) {
                router.get(`/aset/${match[1]}`);
                return;
            }
        }
        router.get(route('aset.index'), { search: query });
    };

    // Camera scanner start/stop logic
    useEffect(() => {
        let scanner = null;
        if (isScanModalOpen && scannerLibLoaded && activeScanTab === 'camera' && window.Html5Qrcode) {
            const Html5Qrcode = window.Html5Qrcode;
            scanner = new Html5Qrcode("qr-camera-reader");

            const handleScanSuccess = (decodedText) => {
                scanner.stop().then(() => {
                    setIsScanModalOpen(false);
                    handleDecodedQR(decodedText);
                }).catch(err => console.error("Error stopping scanner:", err));
            };

            const handleScanFailure = (err) => {
                // silent failure
            };

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length > 0) {
                    setCameras(devices);
                    const defaultCam = devices[0].id;
                    setSelectedCameraId(prev => prev || defaultCam);
                    
                    const activeCamId = selectedCameraId || defaultCam;
                    scanner.start(
                        activeCamId,
                        {
                            fps: 10,
                            qrbox: (width, height) => {
                                const size = Math.min(width, height) * 0.7;
                                return { width: size, height: size };
                            }
                        },
                        handleScanSuccess,
                        handleScanFailure
                    ).catch(err => {
                        setScanError("Gagal membuka kamera: " + err);
                    });
                } else {
                    setScanError("Kamera tidak ditemukan.");
                }
            }).catch(err => {
                setScanError("Error mendeteksi kamera: " + err);
            });
        }

        return () => {
            if (scanner && scanner.isScanning) {
                scanner.stop().catch(err => console.error("Cleanup error:", err));
            }
        };
    }, [isScanModalOpen, scannerLibLoaded, activeScanTab, selectedCameraId]);

    const handleFileUpload = (e) => {
        const file = e.target.files[0];
        if (!file || !window.Html5Qrcode) return;

        setScanError('');
        const Html5Qrcode = window.Html5Qrcode;
        const tempReader = new Html5Qrcode("qr-file-reader-dummy");

        tempReader.scanFile(file, true)
            .then(decodedText => {
                setIsScanModalOpen(false);
                handleDecodedQR(decodedText);
            })
            .catch(err => {
                setScanError("Gagal mendeteksi QR Code dari gambar. Pastikan gambar QR Code terlihat jelas.");
            });
    };
    
    // Toggle Right Sidebar state (persist in localStorage)
    const [isRightSidebarOpen, setIsRightSidebarOpen] = useState(() => {
        const saved = localStorage.getItem('rightSidebarOpen');
        return saved !== null ? JSON.parse(saved) : true;
    });

    const toggleRightSidebar = () => {
        setIsRightSidebarOpen(prev => {
            const next = !prev;
            localStorage.setItem('rightSidebarOpen', JSON.stringify(next));
            return next;
        });
    };

    // Reschedule states
    const [showRescheduleModal, setShowRescheduleModal] = useState(false);
    const [rescheduleDate, setRescheduleDate] = useState('');

    // Helper to determine if link is active
    const isRouteActive = (routePattern) => {
        return route().current(routePattern);
    };

    // Day & Date helpers
    const getIndoDayLabel = (date) => {
        const days = ['M', 'S', 'S', 'R', 'K', 'J', 'S']; // Minggu, Senin, Selasa, Rabu, Kamis, Jumat, Sabtu
        return days[date.getDay()];
    };

    const isSameDate = (d1, d2) => {
        return d1.getFullYear() === d2.getFullYear() &&
               d1.getMonth() === d2.getMonth() &&
               d1.getDate() === d2.getDate();
    };

    // Generate 4 days starting from startDate
    const calendarDays = [];
    for (let i = 0; i < 4; i++) {
        const day = new Date(startDate);
        day.setDate(startDate.getDate() + i);
        calendarDays.push(day);
    }

    const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];
    const monthsIndoFull = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
    
    const formattedMonthYear = `${months[startDate.getMonth()]} ${startDate.getFullYear()}`;

    // Handle calendar navigation
    const handlePrevDay = () => {
        setStartDate(prev => {
            const next = new Date(prev);
            next.setDate(next.getDate() - 1);
            return next;
        });
    };

    const handleNextDay = () => {
        setStartDate(prev => {
            const next = new Date(prev);
            next.setDate(next.getDate() + 1);
            return next;
        });
    };

    const [activeEventOverride, setActiveEventOverride] = useState(null);

    // Find active event on selected date
    const selectedDateStr = selectedDate.getFullYear() + '-' + 
        String(selectedDate.getMonth() + 1).padStart(2, '0') + '-' + 
        String(selectedDate.getDate()).padStart(2, '0');

    const eventsOnDate = upcomingEvents?.filter(event => event.date === selectedDateStr) || [];

    // Reset activeEventOverride when selectedDate changes
    useEffect(() => {
        setActiveEventOverride(null);
    }, [selectedDate]);

    const activeEvent = activeEventOverride || (eventsOnDate.length > 0 ? eventsOnDate[0] : null);

    // Search redirect logic
    const handleSearch = (e) => {
        if (e.key === 'Enter' && searchText.trim() !== '') {
            const query = searchText.trim();
            
            // Cek jika yang di-scan adalah URL Detail Aset (mendukung versi Native dan Laravel)
            if (query.includes('/aset/')) {
                const match = query.match(/\/aset\/(?:detail\/|show\/)?(\d+)/i) || query.match(/(\d+)$/);
                if (match && match[1]) {
                    router.get(`/aset/${match[1]}`);
                    return;
                }
            }
            
            router.get(route('aset.index'), { search: query });
        }
    };

    // Reschedule actions
    const openReschedule = () => {
        if (activeEvent) {
            setRescheduleDate(activeEvent.date);
        } else {
            setRescheduleDate(selectedDateStr);
        }
        setShowRescheduleModal(true);
    };

    const submitReschedule = (e) => {
        e.preventDefault();
        if (!activeEvent) {
            alert('Silakan pilih tanggal yang memiliki agenda kalibrasi/maintenance terlebih dahulu.');
            setShowRescheduleModal(false);
            return;
        }

        router.post(route('calendar.reschedule'), {
            type: activeEvent.type,
            id: activeEvent.id,
            new_date: rescheduleDate
        }, {
            onSuccess: () => {
                setShowRescheduleModal(false);
            }
        });
    };

    return (
        <div className="h-screen w-screen flex flex-col md:flex-row bg-[#edf2f7] md:p-4 md:gap-4 overflow-hidden font-sans text-slate-800">
            {/* Sidebar (Left side) */}
            <aside className="w-66 bg-[#0a3a60] text-white flex flex-col justify-between shrink-0 shadow-lg rounded-[2rem] hidden md:flex h-full p-4">
                <div className="flex flex-col">
                    {/* Brand header with enlarged Hospital Logo & adjacent IPSRS text */}
                    <div className="p-4 flex items-center space-x-3 mb-6">
                        <div className="w-16 h-16 bg-white rounded-2xl flex items-center justify-center shrink-0 shadow-inner p-1">
                            <img 
                                src="/uploads/assets/logo-rs.png" 
                                alt="Logo RS" 
                                className="h-full w-full object-contain"
                            />
                        </div>
                        <div>
                            <span className="text-base text-white font-black block leading-none">MedTrack</span>
                            <span className="text-xs text-white font-bold block mt-1">IPSRS</span>
                        </div>
                    </div>

                    {/* Navigation Items (arranged vertically, matching the screenshot) */}
                    <nav className="space-y-1.5 px-2">
                        {/* 1. Dashboard */}
                        <Link
                            href={route('dashboard')}
                            className={`flex items-center px-4 py-3 rounded-xl text-sm font-semibold tracking-wide transition ${
                                isRouteActive('dashboard')
                                    ? 'bg-white text-[#0a3a60] shadow-sm'
                                    : 'text-slate-100 hover:bg-white/10'
                            }`}
                        >
                            Dashboard
                        </Link>

                        {/* 2. Direktori Unit & SDM */}
                        <Link
                            href={route('direktori.index')}
                            className={`flex items-center px-4 py-3 rounded-xl text-sm font-semibold tracking-wide transition ${
                                isRouteActive('direktori.*')
                                    ? 'bg-white text-[#0a3a60] shadow-sm'
                                    : 'text-slate-100 hover:bg-white/10'
                            }`}
                        >
                            Ruangan & SDM
                        </Link>

                        {/* 3. Aset Medis */}
                        <Link
                            href={route('aset.index')}
                            className={`flex items-center px-4 py-3 rounded-xl text-sm font-semibold tracking-wide transition ${
                                isRouteActive('aset.*')
                                    ? 'bg-white text-[#0a3a60] shadow-sm'
                                    : 'text-slate-100 hover:bg-white/10'
                            }`}
                        >
                            Aset Medis
                        </Link>

                        {/* 4. Mutasi */}
                        <Link
                            href={route('mutasi.index')}
                            className={`flex items-center px-4 py-3 rounded-xl text-sm font-semibold tracking-wide transition ${
                                isRouteActive('mutasi.*')
                                    ? 'bg-white text-[#0a3a60] shadow-sm'
                                    : 'text-slate-100 hover:bg-white/10'
                            }`}
                        >
                            Mutasi Aset
                        </Link>

                        {/* 6. Maintenance */}
                        {['Admin_IPSRS', 'Staf_IPSRS'].includes(user.role) && (
                            <Link
                                href={route('maintenance.index')}
                                className={`flex items-center px-4 py-3 rounded-xl text-sm font-semibold tracking-wide transition ${
                                    isRouteActive('maintenance.*')
                                        ? 'bg-white text-[#0a3a60] shadow-sm'
                                        : 'text-slate-100 hover:bg-white/10'
                                }`}
                            >
                                Preventive Maintenance
                            </Link>
                        )}

                        {/* 7. Work Order */}
                        <Link
                            href={route('workorder.index')}
                            className={`flex items-center px-4 py-3 rounded-xl text-sm font-semibold tracking-wide transition ${
                                isRouteActive('workorder.*')
                                    ? 'bg-white text-[#0a3a60] shadow-sm'
                                    : 'text-slate-100 hover:bg-white/10'
                            }`}
                        >
                            Work Orders (WO)
                        </Link>

                        {/* 8. Dokumen Mutu */}
                        {['Admin_IPSRS', 'Staf_IPSRS', 'Staf_Logistik'].includes(user.role) && (
                            <Link
                                href={route('dokumen.index')}
                                className={`flex items-center px-4 py-3 rounded-xl text-sm font-semibold tracking-wide transition ${
                                    isRouteActive('dokumen.index')
                                        ? 'bg-white text-[#0a3a60] shadow-sm'
                                        : 'text-slate-100 hover:bg-white/10'
                                }`}
                            >
                                Dokumen Mutu
                            </Link>
                        )}
                    </nav>
                </div>

                {/* Bottom of sidebar: Log Out */}
                <div className="px-2 pb-2">
                    <Link
                        href={route('logout')}
                        method="post"
                        as="button"
                        className="w-full flex items-center px-4 py-3 rounded-xl text-sm font-semibold tracking-wide text-white/80 hover:bg-white/10 hover:text-white transition text-left"
                    >
                        Log Out
                    </Link>
                </div>
            </aside>

            {/* Mobile Header (fallback) */}
            <div className="md:hidden w-full flex flex-col absolute top-0 z-40 bg-[#0a3a60] text-white">
                <div className="flex justify-between items-center px-4 py-3">
                    <div className="flex items-center space-x-2">
                        <img src="/uploads/assets/logo-rs.png" alt="Logo RS" className="h-8 w-auto bg-white p-1 rounded" />
                        <span className="font-bold text-xs uppercase">MEDTRACK</span>
                    </div>
                    <button
                        onClick={() => setShowingNavigationDropdown(!showingNavigationDropdown)}
                        className="p-1 rounded-md text-white hover:bg-[#0c4775] focus:outline-none"
                    >
                        ☰
                    </button>
                </div>

                {showingNavigationDropdown && (
                    <nav className="px-4 py-2 border-t border-[#082d4c] bg-[#0a3a60] space-y-1 text-sm font-semibold">
                        <Link href={route('dashboard')} className="block py-2">Dashboard</Link>
                        <Link href={route('direktori.index')} className="block py-2">Ruangan & SDM</Link>
                        <Link href={route('aset.index')} className="block py-2">Aset Medis</Link>
                        <Link href={route('mutasi.index')} className="block py-2">Mutasi Aset</Link>
                        {['Admin_IPSRS', 'Staf_IPSRS'].includes(user.role) && (
                            <Link href={route('maintenance.index')} className="block py-2">Preventive Maintenance</Link>
                        )}
                        <Link href={route('workorder.index')} className="block py-2">Work Orders</Link>
                        {['Admin_IPSRS', 'Staf_IPSRS', 'Staf_Logistik'].includes(user.role) && (
                            <Link href={route('dokumen.index')} className="block py-2">Dokumen Mutu</Link>
                        )}
                        <Link href={route('logout')} method="post" as="button" className="block py-2 text-rose-300">Log Out</Link>
                    </nav>
                )}
            </div>

            {/* Main Area */}
            <div className="flex-1 flex flex-row gap-4 h-full min-w-0 overflow-hidden pt-12 md:pt-0">
                {/* Center Content Column */}
                <div className="flex-1 flex flex-col gap-4 h-full min-w-0 overflow-hidden">
                    {/* Top header bar (always visible) */}
                    <header className="h-20 bg-white md:rounded-2xl flex items-center justify-between px-6 shrink-0 shadow-sm z-30">
                        {/* Left: Functional search via QR Code */}
                        <div className="relative w-80 hidden sm:block">
                            <button
                                type="button"
                                onClick={() => setIsScanModalOpen(true)}
                                title="Buka Kamera Scan QR Code"
                                className="absolute inset-y-0 left-0 pl-3.5 flex items-center text-slate-400 hover:text-[#0a3a60] transition"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-2.25zM3.75 14.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-2.25zM14.625 3.75c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v2.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125v-2.25zM3.75 9.75h3M12 3.75v3M12 9.75h3M20.25 9.75h-3M16.5 12V9.75m0 7.5v-3m0 3H12m3-3v3m1.5-6h3m-3 9v-3m0 3H20.25" />
                                </svg>
                            </button>
                            <input
                                type="text"
                                placeholder="Scan QR Code / Kode Aset..."
                                value={searchText}
                                onChange={(e) => setSearchText(e.target.value)}
                                onKeyDown={handleSearch}
                                className="w-full bg-slate-50 border border-slate-200/80 rounded-full pl-10 pr-4 py-2 text-xs text-slate-700 focus:ring-1 focus:ring-[#0a3a60] focus:border-[#0a3a60] focus:bg-white transition"
                            />
                        </div>

                        {/* Right: Actions, Notification, Sidebar Toggle, Profile */}
                        <div className="flex items-center space-x-3">
                            {/* Notification Bell */}
                            <Dropdown>
                                <Dropdown.Trigger>
                                    <button className="h-9 w-9 bg-slate-50 hover:bg-slate-100 text-slate-600 rounded-full flex items-center justify-center text-sm shadow-sm transition relative cursor-pointer">
                                        🔔
                                        {activeNotifications && activeNotifications.length > 0 && (
                                            <span className="absolute -top-1 -right-1 bg-red-500 text-white text-[8px] font-black w-4 h-4 rounded-full flex items-center justify-center px-1 animate-pulse border border-white">
                                                {activeNotifications.length}
                                            </span>
                                        )}
                                    </button>
                                </Dropdown.Trigger>

                                <Dropdown.Content align="right" width="80" contentClasses="py-1 bg-white rounded-2xl shadow-xl border border-slate-100 max-h-96 overflow-y-auto w-80">
                                    <div className="px-4 py-2.5 border-b border-slate-100">
                                        <span className="font-extrabold text-xs text-[#0a3a60]">Pemberitahuan</span>
                                    </div>
                                    {activeNotifications && activeNotifications.length > 0 ? (
                                        activeNotifications.map((notif, index) => (
                                            <Dropdown.Link
                                                key={notif.id || index}
                                                href={notif.link}
                                                onClick={() => handleNotifClick(notif.id, notif.text)}
                                                className="block px-4 py-3 hover:bg-slate-50 border-b border-slate-50 last:border-b-0"
                                            >
                                                <div className="flex flex-col space-y-1">
                                                    <span className="text-xs text-slate-700 font-semibold leading-relaxed">
                                                        {notif.text}
                                                    </span>
                                                    <span className={`text-[9px] font-bold uppercase tracking-wider ${
                                                        notif.type === 'danger' ? 'text-rose-600' :
                                                        notif.type === 'warning' ? 'text-amber-600' : 'text-sky-600'
                                                    }`}>
                                                        {notif.type}
                                                    </span>
                                                </div>
                                            </Dropdown.Link>
                                        ))
                                    ) : (
                                        <div className="px-4 py-6 text-center">
                                            <span className="text-xs text-slate-400 font-bold block">Tidak ada pemberitahuan baru</span>
                                        </div>
                                    )}
                                </Dropdown.Content>
                            </Dropdown>

                            {/* Toggle Sidebar Button */}
                            <button 
                                onClick={toggleRightSidebar}
                                title={isRightSidebarOpen ? "Tutup Sidebar Kanan" : "Buka Sidebar Kanan"}
                                className={`h-9 w-9 rounded-full flex items-center justify-center text-sm shadow-sm transition ${
                                    isRightSidebarOpen 
                                        ? 'bg-indigo-50 text-indigo-600 hover:bg-indigo-100' 
                                        : 'bg-slate-50 text-slate-600 hover:bg-slate-100'
                                }`}
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" strokeWidth="2.5" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h5a2 2 0 002-2V7a2 2 0 00-2-2h-5a2 2 0 00-2 2" />
                                </svg>
                            </button>

                            <div className="text-right hidden sm:block">
                                <div className="text-sm font-bold text-slate-800 leading-tight">{user.nama_lengkap}</div>
                                <div className="text-[10px] text-sky-600 font-extrabold uppercase tracking-widest mt-0.5">
                                    {user.role.replace('_', ' ')}
                                </div>
                            </div>

                            <Dropdown>
                                <Dropdown.Trigger>
                                    <button className="h-10 w-10 rounded-full overflow-hidden border border-slate-200 flex items-center justify-center shadow-md hover:opacity-90 transition focus:outline-none">
                                        {user.profile_photo_url ? (
                                            <img 
                                                src={user.profile_photo_url} 
                                                alt={user.nama_lengkap} 
                                                className="h-full w-full object-cover"
                                            />
                                        ) : (
                                            <div className="h-full w-full bg-[#0a3a60] text-white flex items-center justify-center text-sm font-bold">
                                                {user.nama_lengkap.charAt(0)}
                                            </div>
                                        )}
                                    </button>
                                </Dropdown.Trigger>

                                <Dropdown.Content>
                                    <Dropdown.Link href={route('profile.edit')}>Edit Profil</Dropdown.Link>
                                    <Dropdown.Link href={route('logout')} method="post" as="button">
                                        Log Out
                                    </Dropdown.Link>
                                </Dropdown.Content>
                            </Dropdown>
                        </div>
                    </header>

                    {/* Scrollable Content Container */}
                    <div className="flex-1 overflow-y-auto md:rounded-2xl flex flex-col gap-4 p-4 md:p-0">
                        {/* Subheader / Page header */}
                        {header && (
                            <div className="bg-white md:rounded-2xl p-6 shadow-sm shrink-0">
                                {header}
                            </div>
                        )}

                        {/* Main page content area */}
                        <main className="flex-1">
                            {children}
                        </main>
                    </div>
                </div>

                {/* Right Sidebar (shown dynamically on toggle) */}
                {isRightSidebarOpen && (
                    <aside className="w-80 bg-white rounded-[2rem] shadow-lg hidden lg:flex flex-col h-full overflow-hidden shrink-0 border border-slate-100 p-5 space-y-4">
                        {/* Calendar Agenda Widget Title */}
                        <div className="flex justify-between items-center shrink-0">
                            <h3 className="font-extrabold text-sm text-[#0a3a60]">Agenda Kalibrasi</h3>
                            <button 
                                onClick={toggleRightSidebar}
                                title="Sembunyikan Sidebar"
                                className="text-slate-400 hover:text-slate-600 transition text-sm font-black"
                            >
                                ✕
                            </button>
                        </div>
                        
                        {/* Navigation Header */}
                        <div className="flex justify-between items-center text-xs text-slate-500 bg-slate-50/50 px-2 py-1 rounded-lg shrink-0">
                            <button onClick={handlePrevDay} className="p-1 hover:text-slate-800 transition font-black text-sm">‹</button>
                            <span className="font-extrabold text-slate-700 tracking-wide uppercase text-[10px]">
                                {formattedMonthYear}
                            </span>
                            <button onClick={handleNextDay} className="p-1 hover:text-slate-800 transition font-black text-sm">›</button>
                        </div>

                        {/* 4-Day Calendar Strip */}
                        <div className="grid grid-cols-4 gap-2 shrink-0">
                            {calendarDays.map((day, idx) => {
                                const isSelected = isSameDate(day, selectedDate);
                                const dayStr = day.getFullYear() + '-' + 
                                    String(day.getMonth() + 1).padStart(2, '0') + '-' + 
                                    String(day.getDate()).padStart(2, '0');
                                const hasEvent = upcomingEvents?.some(event => event.date === dayStr);

                                return (
                                    <button
                                        key={idx}
                                        onClick={() => setSelectedDate(day)}
                                        className={`flex flex-col items-center py-2 px-1 rounded-2xl transition-all duration-150 relative ${
                                            isSelected 
                                                ? 'bg-red-600 text-white shadow-md shadow-red-200' 
                                                : 'bg-slate-50 hover:bg-slate-100 text-slate-700'
                                        }`}
                                    >
                                        <span className={`text-[10px] uppercase font-extrabold ${isSelected ? 'text-red-100' : 'text-slate-400'}`}>
                                            {getIndoDayLabel(day)}
                                        </span>
                                        <span className="text-sm font-black mt-1 mb-1">
                                            {String(day.getDate()).padStart(2, '0')}
                                        </span>
                                        {hasEvent && (
                                            <span className={`w-1 h-1 rounded-full absolute bottom-1 ${
                                                isSelected ? 'bg-white' : 'bg-red-500'
                                            }`} />
                                        )}
                                    </button>
                                );
                            })}
                        </div>

                        {/* Scrollable Widget Container */}
                        <div className="flex-1 overflow-y-auto space-y-5 pr-1 -mr-2">
                            {/* Selected Day Agenda Card */}
                            <div className="border border-slate-100 rounded-3xl p-4 bg-slate-50/40 relative overflow-hidden space-y-4">
                                <div className="space-y-3">
                                    {/* Image of Hospital (background) */}
                                    <div className="w-full h-32 rounded-2xl overflow-hidden shadow-inner border border-slate-100 bg-slate-100 relative">
                                        <img 
                                            src="/uploads/assets/bg-rs.jpeg" 
                                            alt="RS Hasna Medika" 
                                            className="w-full h-full object-cover"
                                        />
                                        {activeEvent && (
                                            <div className="absolute inset-0 bg-black/30 flex items-end p-2.5">
                                                <span className="text-[9px] font-extrabold text-white bg-indigo-600 px-2 py-0.5 rounded uppercase tracking-wider">
                                                    {activeEvent.type}
                                                </span>
                                            </div>
                                        )}
                                    </div>

                                    {/* Multi-event selector tabs */}
                                    {eventsOnDate.length > 1 && (
                                        <div className="flex flex-wrap gap-1 border-b border-slate-100 pb-2">
                                            {eventsOnDate.map((ev, index) => {
                                                const isActive = activeEvent && activeEvent.id === ev.id && activeEvent.type === ev.type;
                                                return (
                                                    <button
                                                        key={ev.id + '_' + ev.type + '_' + index}
                                                        type="button"
                                                        onClick={() => setActiveEventOverride(ev)}
                                                        className={`px-2 py-1 text-[9px] font-extrabold rounded-lg border transition ${
                                                            isActive
                                                                ? 'bg-[#0a3a60] text-white border-[#0a3a60] shadow-sm'
                                                                : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50'
                                                        }`}
                                                    >
                                                        {ev.kode_label || `Agenda ${index + 1}`}
                                                    </button>
                                                );
                                            })}
                                        </div>
                                    )}

                                    {/* Event specifics */}
                                    <div className="space-y-2">
                                        {/* Date */}
                                        <div className="flex items-center space-x-2 text-xs font-semibold text-slate-700">
                                            <span className="text-base text-red-500 font-normal">📅</span>
                                            <span>
                                                {selectedDate.getDate()} {monthsIndoFull[selectedDate.getMonth()]} {selectedDate.getFullYear()}
                                            </span>
                                        </div>

                                        {/* Location */}
                                        <div className="flex items-center space-x-2 text-xs font-semibold text-slate-700">
                                            <span className="text-base text-red-500 font-normal">📍</span>
                                            <span className="truncate">
                                                {activeEvent ? activeEvent.location : 'RS Hasna Medika'}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                {/* Reschedule & Detail buttons (Only show when there is an active event) */}
                                {activeEvent && (
                                    <div className="space-y-2">
                                        <div className="flex space-x-2">
                                            <button
                                                type="button"
                                                onClick={openReschedule}
                                                className="flex-1 py-2 bg-white hover:bg-slate-50 border border-slate-200 text-slate-700 text-xs font-bold rounded-xl transition shadow-sm"
                                            >
                                                Reschedule
                                            </button>
                                            <Link
                                                href={activeEvent.type === 'calibration' ? `/aset/${activeEvent.id}` : route('maintenance.index')}
                                                className="flex-1 py-2 bg-red-600 hover:bg-red-700 text-white text-xs font-bold rounded-xl transition text-center shadow-sm"
                                            >
                                                Detail →
                                            </Link>
                                        </div>
                                    </div>
                                )}

                                    {/* Dynamic message/agenda details */}
                                    <div className="pt-2.5 border-t border-slate-100">
                                        {activeEvent ? (
                                            <div className="bg-indigo-50/50 border border-indigo-100/50 p-2.5 rounded-xl">
                                                <span className="text-[10px] font-bold text-indigo-950 block">
                                                    {activeEvent.title}
                                                </span>
                                                {activeEvent.kode_label && (
                                                    <span className="text-[9px] text-slate-500 font-semibold block mt-1">
                                                        {activeEvent.kode_label} • {activeEvent.location}
                                                    </span>
                                                )}
                                            </div>
                                        ) : (
                                            <div className="text-center py-2 bg-slate-50/80 rounded-xl border border-dashed border-slate-200 p-2">
                                                <span className="text-[10px] text-slate-450 font-bold block">
                                                    Tidak ada data khusus untuk halaman ini.
                                                </span>
                                            </div>
                                        )}
                                    </div>
                                </div>

                            {/* Upcoming Calibration List Widget */}
                            <div className="space-y-3">
                                <div className="flex items-center space-x-2">
                                    <span className="text-base">⏱️</span>
                                    <h4 className="font-extrabold text-sm text-[#0a3a60]">Kalibrasi Mendatang</h4>
                                </div>

                                <div className="space-y-2">
                                    {upcomingCalibrations && upcomingCalibrations.length > 0 ? (
                                        upcomingCalibrations.map((cal) => {
                                            // Determine badge color based on remaining days
                                            let badgeStyle = 'bg-indigo-50 text-indigo-700 border-indigo-100';
                                            if (cal.sisa_hari <= 3) {
                                                badgeStyle = 'bg-rose-50 text-rose-700 border-rose-100 animate-pulse font-bold';
                                            } else if (cal.sisa_hari <= 7) {
                                                badgeStyle = 'bg-amber-50 text-amber-700 border-amber-100 font-bold';
                                            }

                                            return (
                                                <div 
                                                    key={cal.id_aset} 
                                                    className="p-3 bg-slate-50/50 border border-slate-100 rounded-2xl hover:bg-slate-100/55 transition cursor-pointer flex justify-between items-center gap-2"
                                                    onClick={() => router.get(route('aset.show', cal.id_aset))}
                                                >
                                                    <div className="min-w-0 flex-1">
                                                        <span className="text-[10px] font-mono text-indigo-600 font-semibold block">
                                                            {cal.kode_label}
                                                        </span>
                                                        <span className="text-xs font-extrabold text-slate-800 block truncate mt-0.5" title={cal.nama_alat}>
                                                            {cal.nama_alat}
                                                        </span>
                                                        <span className="text-[10px] text-slate-450 font-semibold block mt-0.5">
                                                            Exp: {cal.tgl_kadaluarsa_formatted} • {cal.nama_ruang}
                                                        </span>
                                                    </div>
                                                    <div className="shrink-0">
                                                        <span className={`inline-block px-2 py-1 border text-[9px] uppercase tracking-wider rounded-lg ${badgeStyle}`}>
                                                            H-{cal.sisa_hari}
                                                        </span>
                                                    </div>
                                                </div>
                                            );
                                        })
                                    ) : (
                                        <div className="text-center py-4 bg-slate-50/80 rounded-2xl border border-dashed border-slate-200">
                                            <span className="text-[10px] text-slate-450 font-bold block">
                                                Tidak ada alat mendekati waktu kalibrasi.
                                            </span>
                                        </div>
                                    )}
                                </div>
                            </div>
                        </div>
                    </aside>
                )}
            </div>

            {/* Reschedule Modal */}
            {showRescheduleModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                    <div className="bg-white rounded-3xl w-full max-w-md p-6 space-y-6 shadow-2xl relative">
                        <div className="flex justify-between items-start border-b border-gray-100 pb-3">
                            <h3 className="font-bold text-lg text-indigo-950">Reschedule Agenda</h3>
                            <button onClick={() => setShowRescheduleModal(false)} className="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                        </div>
                        
                        <form onSubmit={submitReschedule} className="space-y-4 text-sm text-slate-700">
                            <p className="text-xs text-gray-500">
                                Ubah tanggal rencana agenda: <span className="font-bold text-[#0a3a60]">{activeEvent ? activeEvent.title : 'Agenda Kalibrasi Aset'}</span>
                            </p>
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Tanggal Rencana Baru *</label>
                                <input
                                    type="date"
                                    required
                                    value={rescheduleDate}
                                    onChange={(e) => setRescheduleDate(e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#0a3a60]"
                                />
                            </div>
                            <div className="flex justify-end space-x-2 pt-4 border-t border-gray-100">
                                <button
                                    type="button"
                                    onClick={() => setShowRescheduleModal(false)}
                                    className="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-semibold text-slate-500"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    className="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl font-semibold shadow-sm transition"
                                >
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* QR Code Scanner Modal */}
            {isScanModalOpen && (
                <div className="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-50 flex items-center justify-center p-4">
                    <div className="bg-white rounded-3xl max-w-md w-full shadow-2xl overflow-hidden border border-slate-100 animate-in fade-in zoom-in duration-200">
                        {/* Header */}
                        <div className="p-6 border-b border-slate-100 flex justify-between items-center bg-[#0a3a60] text-white">
                            <div>
                                <h3 className="font-extrabold text-lg flex items-center gap-2">
                                    🔲 Scan QR Code Aset
                                </h3>
                                <p className="text-xs text-slate-200 mt-1">Arahkan kamera ke QR Code label alat medis</p>
                            </div>
                            <button 
                                type="button"
                                onClick={() => setIsScanModalOpen(false)}
                                className="h-8 w-8 rounded-full bg-white/10 hover:bg-white/25 flex items-center justify-center font-bold transition text-lg"
                            >
                                &times;
                            </button>
                        </div>

                        {/* Tabs */}
                        <div className="flex border-b border-slate-150 text-sm">
                            <button
                                type="button"
                                onClick={() => { setActiveScanTab('camera'); setScanError(''); }}
                                className={`flex-1 py-3 font-semibold text-center transition ${
                                    activeScanTab === 'camera' 
                                        ? 'border-b-2 border-[#0a3a60] text-[#0a3a60]' 
                                        : 'text-slate-500 hover:text-slate-800'
                                }`}
                            >
                                📷 Kamera Utama
                            </button>
                            <button
                                type="button"
                                onClick={() => { setActiveScanTab('file'); setScanError(''); }}
                                className={`flex-1 py-3 font-semibold text-center transition ${
                                    activeScanTab === 'file' 
                                        ? 'border-b-2 border-[#0a3a60] text-[#0a3a60]' 
                                        : 'text-slate-500 hover:text-slate-800'
                                }`}
                            >
                                📂 Unggah Gambar
                            </button>
                        </div>

                        {/* Content Area */}
                        <div className="p-6">
                            {/* Camera View */}
                            {activeScanTab === 'camera' && (
                                <div className="space-y-4 flex flex-col items-center">
                                    {/* Camera Selector */}
                                    {cameras.length > 1 && (
                                        <div className="w-full">
                                            <label className="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Pilih Kamera</label>
                                            <select
                                                value={selectedCameraId}
                                                onChange={(e) => setSelectedCameraId(e.target.value)}
                                                className="w-full text-xs bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-slate-700 focus:ring-1 focus:ring-[#0a3a60] outline-none"
                                            >
                                                {cameras.map((device, idx) => (
                                                    <option key={device.id} value={device.id}>
                                                        {device.label || `Kamera ${idx + 1}`}
                                                    </option>
                                                ))}
                                            </select>
                                        </div>
                                    )}

                                    {/* Scanning Box Container */}
                                    <div className="w-full aspect-square max-w-[240px] bg-slate-900 rounded-2xl overflow-hidden relative border-2 border-slate-800 shadow-inner flex items-center justify-center">
                                        <div id="qr-camera-reader" className="w-full h-full object-cover"></div>
                                        
                                        {/* Scanner Red Laser Line Animation */}
                                        <div className="absolute inset-x-0 h-[2px] bg-red-500 shadow-[0_0_8px_rgba(239,68,68,0.8)] animate-pulse top-0 left-0" 
                                             style={{
                                                 animation: 'laser-scan 2.5s infinite linear',
                                             }}
                                        ></div>

                                        {/* Scanner Overlay Corners */}
                                        <div className="absolute top-4 left-4 w-6 h-6 border-t-4 border-l-4 border-emerald-500 rounded-tl-md"></div>
                                        <div className="absolute top-4 right-4 w-6 h-6 border-t-4 border-r-4 border-emerald-500 rounded-tr-md"></div>
                                        <div className="absolute bottom-4 left-4 w-6 h-6 border-b-4 border-l-4 border-emerald-500 rounded-bl-md"></div>
                                        <div className="absolute bottom-4 right-4 w-6 h-6 border-b-4 border-r-4 border-emerald-500 rounded-br-md"></div>
                                    </div>
                                    
                                    <p className="text-xs text-slate-400 text-center animate-pulse">Menghubungkan ke kamera...</p>
                                </div>
                            )}

                            {/* File Upload View */}
                            {activeScanTab === 'file' && (
                                <div className="space-y-4">
                                    <label className="border-2 border-dashed border-slate-200 hover:border-indigo-400 rounded-2xl p-8 flex flex-col items-center justify-center cursor-pointer transition bg-slate-50 hover:bg-indigo-50/20 group">
                                        <span className="text-4xl group-hover:scale-110 transition duration-200">🖼️</span>
                                        <span className="text-sm font-bold text-slate-700 mt-3">Pilih Foto QR Code</span>
                                        <span className="text-xs text-slate-400 mt-1">Format PNG, JPG, atau JPEG</span>
                                        <input 
                                            type="file" 
                                            accept="image/*" 
                                            onChange={handleFileUpload} 
                                            className="hidden" 
                                        />
                                    </label>
                                    
                                    {/* Dummy container for file scan */}
                                    <div id="qr-file-reader-dummy" className="hidden"></div>
                                </div>
                            )}

                            {/* Error Message */}
                            {scanError && (
                                <div className="mt-4 p-3 bg-rose-50 border border-rose-100 rounded-xl flex items-start space-x-2 text-rose-700 text-xs">
                                    <span className="text-sm">⚠️</span>
                                    <span>{scanError}</span>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            )}

            {/* Custom Scan Animation CSS */}
            <style dangerouslySetInnerHTML={{__html: `
                @keyframes laser-scan {
                    0% { top: 5%; }
                    50% { top: 95%; }
                    100% { top: 5%; }
                }
            `}} />
        </div>
    );
}
