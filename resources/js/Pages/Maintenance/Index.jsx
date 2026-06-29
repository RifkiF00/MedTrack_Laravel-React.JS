import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Index({ auth, scheduledItems, completedLogs, masterItems, stats }) {
    const [selectedLog, setSelectedLog] = useState(null);
    const [showLogModal, setShowLogModal] = useState(false);
    
    // Modal controls for general log input and history
    const [showGeneralLogModal, setShowGeneralLogModal] = useState(false);
    const [showHistoryModal, setShowHistoryModal] = useState(false);

    // Master Items CRUD states
    const [editingMaster, setEditingMaster] = useState(null);
    const [showEditMasterModal, setShowEditMasterModal] = useState(false);

    // Log Items Edit states
    const [editingLog, setEditingLog] = useState(null);
    const [showEditLogModal, setShowEditLogModal] = useState(false);

    const masterForm = useForm({
        nama_item: '',
        deskripsi: '',
        lokasi: '',
        frekuensi: 'Bulanan',
        catatan: ''
    });

    const openEditMasterModal = (item) => {
        setEditingMaster(item);
        masterForm.setData({
            nama_item: item.nama_item || '',
            deskripsi: item.deskripsi || '',
            lokasi: item.lokasi || '',
            frekuensi: item.frekuensi || 'Bulanan',
            catatan: item.catatan || ''
        });
        setShowEditMasterModal(true);
    };

    const handleUpdateMaster = (e) => {
        e.preventDefault();
        masterForm.post(route('maintenance.master.update', editingMaster.id_pemeliharaan), {
            onSuccess: () => {
                setShowEditMasterModal(false);
                setEditingMaster(null);
                masterForm.reset();
            }
        });
    };

    const handleDeleteMaster = (id) => {
        if (confirm('Yakin ingin menghapus jadwal pemeliharaan rutin ini? Semua log pelaksanaan terkait juga akan dihapus.')) {
            router.post(route('maintenance.master.delete', id));
        }
    };

    // Form: Record log check (for a specific scheduled item)
    const logForm = useForm({
        id_log: '',
        hasil_pengecekan: '',
        kondisi_laporan: 'Normal',
        catatan_khusus: '',
        tgl_rencana_berikutnya: ''
    });

    const openLogModal = (log) => {
        setSelectedLog(log);
        logForm.setData({
            id_log: log.id_log,
            hasil_pengecekan: '',
            kondisi_laporan: 'Normal',
            catatan_khusus: '',
            tgl_rencana_berikutnya: ''
        });
        setShowLogModal(true);
    };

    const handleSaveLog = (e) => {
        e.preventDefault();
        logForm.post(route('maintenance.log'), {
            onSuccess: () => {
                setShowLogModal(false);
                setSelectedLog(null);
                logForm.reset();
            }
        });
    };

    // Form: General log input (where they select the item from a dropdown)
    const generalLogForm = useForm({
        id_log: '',
        id_pemeliharaan: '',
        tgl_rencana: new Date().toISOString().split('T')[0],
        status_pelaksanaan: 'Terselesaikan',
        hasil_pengecekan: '',
        kondisi_laporan: 'Normal',
        catatan_khusus: '',
        tgl_rencana_berikutnya: ''
    });

    const handleGeneralLogSubmit = (e) => {
        e.preventDefault();
        generalLogForm.post(route('maintenance.log'), {
            onSuccess: () => {
                setShowGeneralLogModal(false);
                generalLogForm.reset();
            }
        });
    };

    // Form: Edit completed log check
    const editLogForm = useForm({
        hasil_pengecekan: '',
        kondisi_laporan: 'Normal',
        catatan_khusus: '',
        tgl_pelaksanaan: ''
    });

    const openEditLogModal = (log) => {
        setEditingLog(log);
        editLogForm.setData({
            hasil_pengecekan: log.hasil_pengecekan || '',
            kondisi_laporan: log.kondisi_laporan || 'Normal',
            catatan_khusus: log.catatan_khusus || '',
            tgl_pelaksanaan: log.tgl_pelaksanaan_raw || ''
        });
        setShowEditLogModal(true);
    };

    const handleUpdateLog = (e) => {
        e.preventDefault();
        editLogForm.post(route('maintenance.log.update', editingLog.id_log), {
            onSuccess: () => {
                setShowEditLogModal(false);
                setEditingLog(null);
                editLogForm.reset();
            }
        });
    };

    const handleDeleteLog = (id) => {
        if (confirm('Yakin ingin menghapus riwayat pemeriksaan ini?')) {
            router.post(route('maintenance.log.delete', id));
        }
    };

    // Handler when user clicks "Pending" button on a master item
    const handleOpenPendingLog = (item) => {
        const matchingLog = scheduledItems.find(log => log.id_pemeliharaan === item.id_pemeliharaan);
        if (matchingLog) {
            openLogModal(matchingLog);
        } else {
            generalLogForm.setData({
                id_log: '',
                id_pemeliharaan: item.id_pemeliharaan,
                tgl_rencana: new Date().toISOString().split('T')[0],
                status_pelaksanaan: 'Terselesaikan',
                hasil_pengecekan: '',
                kondisi_laporan: 'Normal',
                catatan_khusus: '',
                tgl_rencana_berikutnya: ''
            });
            setShowGeneralLogModal(true);
        }
    };

    // Count completions today
    const todayStr = new Date().toISOString().split('T')[0];
    const completedTodayCount = completedLogs.filter(log => log.tgl_pelaksanaan?.startsWith(todayStr)).length;

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-extrabold text-2xl text-slate-800 leading-tight">Preventive Maintenance</h2>
                        <p className="text-xs text-slate-500 mt-1">Jadwal dan log pemeliharaan rutin aset untuk menjaga kondisi optimal</p>
                    </div>
                </div>
            }
        >
            <Head title="Preventive Maintenance" />

            <div className="space-y-6 pb-8">
                {/* 4 Stats Cards (Styled with left border matching screenshot) */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    {/* Total Item */}
                    <div className="bg-white p-5 rounded-2xl border-l-4 border-blue-500 shadow-sm flex flex-col justify-between h-24">
                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Item</span>
                        <span className="text-3xl font-black text-slate-800 leading-none">{stats.total_master}</span>
                    </div>

                    {/* Hari Ini */}
                    <div className="bg-white p-5 rounded-2xl border-l-4 border-amber-500 shadow-sm flex flex-col justify-between h-24">
                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Hari Ini</span>
                        <span className="text-3xl font-black text-slate-800 leading-none">{scheduledItems.length}</span>
                    </div>

                    {/* Selesai Hari Ini */}
                    <div className="bg-white p-5 rounded-2xl border-l-4 border-sky-500 shadow-sm flex flex-col justify-between h-24">
                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Selesai Hari Ini</span>
                        <span className="text-3xl font-black text-slate-800 leading-none">{completedTodayCount}</span>
                    </div>

                    {/* Bulan Ini */}
                    <div className="bg-white p-5 rounded-2xl border-l-4 border-slate-500 shadow-sm flex flex-col justify-between h-24">
                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Bulan Ini</span>
                        <span className="text-3xl font-black text-slate-800 leading-none">{stats.completed_month}</span>
                    </div>
                </div>

                {/* Action Buttons Strip */}
                <div className="flex flex-wrap gap-3 items-center">
                    <Link
                        href={route('maintenance.create')}
                        className="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl text-xs font-extrabold shadow-sm transition"
                    >
                        + Tambah Jadwal
                    </Link>

                    <button
                        onClick={() => setShowGeneralLogModal(true)}
                        className="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-xl text-xs font-extrabold shadow-sm transition"
                    >
                        + Input Log
                    </button>

                    <button
                        onClick={() => setShowHistoryModal(true)}
                        className="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl text-xs font-extrabold shadow-sm border border-slate-200 transition"
                    >
                        📑 Riwayat
                    </button>
                </div>

                {/* Jadwal Hari Ini Section */}
                <div className="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 className="font-extrabold text-sm text-slate-800 flex items-center space-x-2">
                        <span>🗓️</span> <span>Jadwal Hari Ini</span>
                    </h3>

                    <div className="divide-y divide-slate-100">
                        {masterItems.length > 0 ? (
                            masterItems.map((item) => (
                                <div key={item.id_pemeliharaan} className="py-4 flex flex-col sm:flex-row sm:justify-between sm:items-center gap-3">
                                    <div>
                                        <h4 className="font-extrabold text-slate-800 text-sm leading-snug">{item.nama_item}</h4>
                                        <p className="text-xs text-slate-450 font-semibold mt-1">
                                            {item.frekuensi} • {item.lokasi}
                                        </p>
                                    </div>
                                    <div className="flex items-center space-x-2 shrink-0">
                                        {/* Pending Button */}
                                        <button
                                            onClick={() => handleOpenPendingLog(item)}
                                            className="flex items-center space-x-1 px-3 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 border border-amber-200 text-xs font-semibold rounded-xl transition shadow-sm"
                                        >
                                            <span>⏱️</span> <span>Pending</span>
                                        </button>

                                        {/* Hapus Button */}
                                        <button
                                            onClick={() => handleDeleteMaster(item.id_pemeliharaan)}
                                            className="flex items-center space-x-1 px-3 py-1.5 bg-rose-50 hover:bg-rose-100 text-rose-600 border border-rose-200 text-xs font-semibold rounded-xl transition shadow-sm"
                                        >
                                            <span>🗑️</span> <span>Hapus</span>
                                        </button>
                                        
                                        {/* Edit Button */}
                                        <button
                                            onClick={() => openEditMasterModal(item)}
                                            className="flex items-center space-x-1 px-3 py-1.5 bg-slate-50 hover:bg-slate-100 text-slate-600 border border-slate-200 text-xs font-semibold rounded-xl transition shadow-sm"
                                        >
                                            <span>Edit</span>
                                        </button>
                                    </div>
                                </div>
                            ))
                        ) : (
                            <p className="text-center py-8 text-xs text-slate-400 font-semibold">Tidak ada jadwal pemeliharaan rutin terdaftar.</p>
                        )}
                    </div>
                </div>

                {/* Log Terbaru Section */}
                <div className="bg-white rounded-[2rem] border border-slate-100 shadow-sm p-6 space-y-4">
                    <h3 className="font-extrabold text-sm text-slate-800 flex items-center space-x-2">
                        <span>📝</span> <span>Log Terbaru</span>
                    </h3>

                    {completedLogs.length > 0 ? (
                        <div className="divide-y divide-slate-100">
                            {completedLogs.slice(0, 5).map((log) => (
                                <div key={log.id_log} className="py-4 flex justify-between items-center">
                                    <div>
                                        <h4 className="font-extrabold text-slate-800 text-sm">{log.nama_item}</h4>
                                        <p className="text-xs text-slate-450 font-semibold mt-1">
                                            Selesai oleh: {log.pelaksana} • {log.tgl_pelaksanaan}
                                        </p>
                                        {log.hasil_pengecekan && (
                                            <p className="text-xs text-slate-600 mt-2 bg-slate-50 p-2.5 rounded-xl border border-slate-100">
                                                Laporan: {log.hasil_pengecekan}
                                            </p>
                                        )}
                                    </div>
                                    <div className="flex flex-col items-end space-y-2 shrink-0">
                                        <span className={`px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider ${
                                            log.kondisi_laporan === 'Normal' ? 'bg-emerald-50 text-emerald-700' :
                                            log.kondisi_laporan === 'Butuh Perbaikan' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700'
                                        }`}>
                                            {log.kondisi_laporan}
                                        </span>
                                        <div className="flex space-x-2">
                                            <button 
                                                onClick={() => openEditLogModal(log)}
                                                className="text-[10px] text-indigo-600 font-bold hover:underline"
                                            >
                                                Edit
                                            </button>
                                            <button 
                                                onClick={() => handleDeleteLog(log.id_log)}
                                                className="text-[10px] text-rose-600 font-bold hover:underline"
                                            >
                                                Hapus
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            ))}
                        </div>
                    ) : (
                        <div className="text-center py-12 bg-slate-50 border border-dashed border-slate-200 rounded-[2rem] p-4">
                            <span className="text-xs text-slate-400 font-bold block">Belum ada log maintenance</span>
                        </div>
                    )}
                </div>
            </div>

            {/* MODALS */}

            {/* Modal: Catat Hasil Pelaksanaan (Specific) */}
            {showLogModal && selectedLog && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                    <div className="bg-white rounded-3xl w-full max-w-xl p-6 space-y-6 shadow-2xl relative">
                        <div className="flex justify-between items-start border-b border-gray-100 pb-3">
                            <div>
                                <h3 className="font-bold text-lg text-indigo-950">Catat Pelaksanaan PM</h3>
                                <p className="text-xs text-gray-500 mt-0.5">{selectedLog.nama_item}</p>
                            </div>
                            <button onClick={() => setShowLogModal(false)} className="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                        </div>

                        <form onSubmit={handleSaveLog} className="space-y-4 text-sm text-slate-700">
                            {/* Hasil Pengecekan */}
                            <div>
                                <label className="block font-semibold text-gray-700">Hasil Pengecekan / Parameter Cek *</label>
                                <textarea
                                    required
                                    value={logForm.data.hasil_pengecekan}
                                    onChange={(e) => logForm.setData('hasil_pengecekan', e.target.value)}
                                    placeholder="Contoh: Kondisi filter dibersihkan, tegangan genset stabil pada 220V..."
                                    rows="3"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            {/* Kondisi Laporan */}
                            <div>
                                <label className="block font-semibold text-gray-700">Status Kondisi *</label>
                                <select
                                    value={logForm.data.kondisi_laporan}
                                    onChange={(e) => logForm.setData('kondisi_laporan', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="Normal">Normal (Alat Beroperasi Baik)</option>
                                    <option value="Butuh Perbaikan">Butuh Perbaikan (Ada sedikit kendala)</option>
                                    <option value="Rusak">Rusak (Harus distop sementara)</option>
                                </select>
                            </div>

                            {/* Catatan Khusus */}
                            <div>
                                <label className="block font-semibold text-gray-700">Catatan Khusus (Opsional)</label>
                                <input
                                    type="text"
                                    value={logForm.data.catatan_khusus}
                                    onChange={(e) => logForm.setData('catatan_khusus', e.target.value)}
                                    placeholder="Tulis instruksi tindak lanjut apabila ada..."
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            {/* Tanggal Rencana Berikutnya */}
                            <div>
                                <label className="block font-semibold text-gray-700">Jadwalkan PM Berikutnya (Opsional)</label>
                                <input
                                    type="date"
                                    value={logForm.data.tgl_rencana_berikutnya}
                                    onChange={(e) => logForm.setData('tgl_rencana_berikutnya', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            {/* Actions */}
                            <div className="border-t border-gray-100 pt-4 flex justify-end space-x-3">
                                <button
                                    type="button"
                                    onClick={() => setShowLogModal(false)}
                                    className="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-semibold text-slate-500 transition"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={logForm.processing}
                                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl font-semibold transition shadow-sm"
                                >
                                    Simpan Log
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal: Input Log Pengecekan (General) */}
            {showGeneralLogModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                    <div className="bg-white rounded-3xl w-full max-w-xl p-6 space-y-6 shadow-2xl relative">
                        <div className="flex justify-between items-start border-b border-gray-100 pb-3">
                            <div>
                                <h3 className="font-bold text-lg text-indigo-950">Input Log Pengecekan Aset</h3>
                                <p className="text-xs text-gray-500 mt-0.5">Catat hasil peninjauan dan kondisi fisik fasilitas</p>
                            </div>
                            <button onClick={() => setShowGeneralLogModal(false)} className="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                        </div>
 
                        <form onSubmit={handleGeneralLogSubmit} className="space-y-4 text-sm text-slate-700">
                            {/* Select Item */}
                            <div>
                                <label className="block font-semibold text-gray-700">Pilih Item Pemeliharaan *</label>
                                <select
                                    required
                                    value={generalLogForm.data.id_pemeliharaan}
                                    onChange={(e) => generalLogForm.setData('id_pemeliharaan', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">-- Pilih Item --</option>
                                    {masterItems.map((item) => (
                                        <option key={item.id_pemeliharaan} value={item.id_pemeliharaan}>
                                            {item.nama_item} ({item.lokasi})
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Tanggal Rencana (Tanggal Pelaksanaan) */}
                            <div>
                                <label className="block font-semibold text-gray-700">Tanggal Pelaksanaan *</label>
                                <input
                                    type="date"
                                    required
                                    value={generalLogForm.data.tgl_rencana}
                                    onChange={(e) => generalLogForm.setData('tgl_rencana', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            {/* Status Pelaksanaan */}
                            <div>
                                <label className="block font-semibold text-gray-700">Status *</label>
                                <select
                                    required
                                    value={generalLogForm.data.status_pelaksanaan}
                                    onChange={(e) => generalLogForm.setData('status_pelaksanaan', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="Terselesaikan">✓ Terselesaikan</option>
                                    <option value="Terjadwal">⏳ Terjadwal</option>
                                    <option value="Tertunda">⚠ Tertunda</option>
                                    <option value="Dibatalkan">✕ Dibatalkan</option>
                                </select>
                            </div>
 
                            {/* Hasil Pengecekan */}
                            <div>
                                <label className="block font-semibold text-gray-700">Hasil Pengecekan / Parameter Cek *</label>
                                <textarea
                                    required
                                    value={generalLogForm.data.hasil_pengecekan}
                                    onChange={(e) => generalLogForm.setData('hasil_pengecekan', e.target.value)}
                                    placeholder="Catat hasil pengecekan/perbaikan..."
                                    rows="3"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
 
                            {/* Kondisi Laporan */}
                            <div>
                                <label className="block font-semibold text-gray-700">Kondisi *</label>
                                <select
                                    value={generalLogForm.data.kondisi_laporan}
                                    onChange={(e) => generalLogForm.setData('kondisi_laporan', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="Normal">✓ Normal</option>
                                    <option value="Butuh Perbaikan">⚠ Perlu Perbaikan</option>
                                    <option value="Rusak">✕ Rusak</option>
                                    <option value="Penggantian Part">🔧 Penggantian Part</option>
                                </select>
                            </div>
 
                            {/* Catatan Khusus */}
                            <div>
                                <label className="block font-semibold text-gray-700">Catatan Khusus (Opsional)</label>
                                <textarea
                                    value={generalLogForm.data.catatan_khusus}
                                    onChange={(e) => generalLogForm.setData('catatan_khusus', e.target.value)}
                                    placeholder="Tulis kendala/tindak lanjut jika ada..."
                                    rows="2"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
 
                            {/* Tanggal Rencana Berikutnya */}
                            <div>
                                <label className="block font-semibold text-gray-700">Jadwalkan PM Berikutnya (Opsional)</label>
                                <input
                                    type="date"
                                    value={generalLogForm.data.tgl_rencana_berikutnya}
                                    onChange={(e) => generalLogForm.setData('tgl_rencana_berikutnya', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>
 
                            {/* Actions */}
                            <div className="border-t border-gray-100 pt-4 flex justify-end space-x-3">
                                <button
                                    type="button"
                                    onClick={() => setShowGeneralLogModal(false)}
                                    className="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-semibold text-slate-500 transition"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={generalLogForm.processing}
                                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl font-semibold transition shadow-sm"
                                >
                                    ✓ Simpan Log
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal: Edit Master Pemeliharaan */}
            {showEditMasterModal && editingMaster && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                    <div className="bg-white rounded-3xl w-full max-w-xl p-6 space-y-6 shadow-2xl relative">
                        <div className="flex justify-between items-start border-b border-gray-100 pb-3">
                            <div>
                                <h3 className="font-bold text-lg text-indigo-950">Edit Master Pemeliharaan</h3>
                                <p className="text-xs text-gray-500 mt-0.5">Ubah spesifikasi atau jadwal pemeliharaan rutin</p>
                            </div>
                            <button onClick={() => setShowEditMasterModal(false)} className="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                        </div>

                        <form onSubmit={handleUpdateMaster} className="space-y-4 text-sm text-slate-700">
                            {/* Nama Item */}
                            <div>
                                <label className="block font-semibold text-gray-700">Nama Item Pemeliharaan *</label>
                                <input
                                    type="text"
                                    required
                                    value={masterForm.data.nama_item}
                                    onChange={(e) => masterForm.setData('nama_item', e.target.value)}
                                    placeholder="Contoh: Pemeliharaan AC Ruang Server..."
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {masterForm.errors.nama_item && <p className="text-rose-500 text-xs mt-1">{masterForm.errors.nama_item}</p>}
                            </div>

                            {/* Deskripsi */}
                            <div>
                                <label className="block font-semibold text-gray-700">Deskripsi Pekerjaan</label>
                                <textarea
                                    value={masterForm.data.deskripsi}
                                    onChange={(e) => masterForm.setData('deskripsi', e.target.value)}
                                    placeholder="Deskripsikan langkah cek..."
                                    rows="2"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                />
                            </div>

                            {/* Lokasi */}
                            <div>
                                <label className="block font-semibold text-gray-700">Lokasi / Area *</label>
                                <input
                                    type="text"
                                    required
                                    value={masterForm.data.lokasi}
                                    onChange={(e) => masterForm.setData('lokasi', e.target.value)}
                                    placeholder="Contoh: Gedung B Lantai 2..."
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {masterForm.errors.lokasi && <p className="text-rose-500 text-xs mt-1">{masterForm.errors.lokasi}</p>}
                            </div>

                            {/* Frekuensi */}
                            <div>
                                <label className="block font-semibold text-gray-700">Frekuensi Pemeliharaan *</label>
                                <select
                                    value={masterForm.data.frekuensi}
                                    onChange={(e) => masterForm.setData('frekuensi', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm"
                                >
                                    <option value="Harian">Harian</option>
                                    <option value="2x_Harian">2x Sehari</option>
                                    <option value="3x_Harian">3x Sehari</option>
                                    <option value="Mingguan">Mingguan</option>
                                    <option value="Bulanan">Bulanan</option>
                                    <option value="3_Bulanan">3 Bulanan</option>
                                    <option value="6_Bulanan">6 Bulanan</option>
                                    <option value="Tahunan">Tahunan</option>
                                </select>
                            </div>

                            {/* Catatan */}
                            <div>
                                <label className="block font-semibold text-gray-700">Catatan Tambahan</label>
                                <input
                                    type="text"
                                    value={masterForm.data.catatan}
                                    onChange={(e) => masterForm.setData('catatan', e.target.value)}
                                    placeholder="Tulis instruksi khusus..."
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm"
                                />
                            </div>

                            {/* Actions */}
                            <div className="border-t border-gray-100 pt-4 flex justify-end space-x-3">
                                <button
                                    type="button"
                                    onClick={() => setShowEditMasterModal(false)}
                                    className="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-semibold text-slate-500 transition"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={masterForm.processing}
                                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl font-semibold transition shadow-sm"
                                >
                                    Perbarui Master
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal: Edit Log Pelaksanaan / Pengecekan */}
            {showEditLogModal && editingLog && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                    <div className="bg-white rounded-3xl w-full max-w-xl p-6 space-y-6 shadow-2xl relative">
                        <div className="flex justify-between items-start border-b border-gray-100 pb-3">
                            <div>
                                <h3 className="font-bold text-lg text-indigo-950">Edit Log Pengecekan</h3>
                                <p className="text-xs text-gray-500 mt-0.5">{editingLog.nama_item}</p>
                            </div>
                            <button onClick={() => setShowEditLogModal(false)} className="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                        </div>

                        <form onSubmit={handleUpdateLog} className="space-y-4 text-sm text-slate-700">
                            {/* Hasil Pengecekan */}
                            <div>
                                <label className="block font-semibold text-gray-700">Hasil Pengecekan / Parameter Cek *</label>
                                <textarea
                                    required
                                    value={editLogForm.data.hasil_pengecekan}
                                    onChange={(e) => editLogForm.setData('hasil_pengecekan', e.target.value)}
                                    rows="3"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                            </div>

                            {/* Kondisi Laporan */}
                            <div>
                                <label className="block font-semibold text-gray-700">Status Kondisi *</label>
                                <select
                                    value={editLogForm.data.kondisi_laporan}
                                    onChange={(e) => editLogForm.setData('kondisi_laporan', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                >
                                    <option value="Normal">Normal (Alat Beroperasi Baik)</option>
                                    <option value="Butuh Perbaikan">Butuh Perbaikan (Ada sedikit kendala)</option>
                                    <option value="Rusak">Rusak (Harus distop sementara)</option>
                                </select>
                            </div>

                            {/* Catatan Khusus */}
                            <div>
                                <label className="block font-semibold text-gray-700">Catatan Khusus (Opsional)</label>
                                <input
                                    type="text"
                                    value={editLogForm.data.catatan_khusus}
                                    onChange={(e) => editLogForm.setData('catatan_khusus', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm"
                                />
                            </div>

                            {/* Tanggal Pelaksanaan */}
                            <div>
                                <label className="block font-semibold text-gray-700">Tanggal Pelaksanaan *</label>
                                <input
                                    type="date"
                                    required
                                    value={editLogForm.data.tgl_pelaksanaan}
                                    onChange={(e) => editLogForm.setData('tgl_pelaksanaan', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none"
                                />
                            </div>

                            {/* Actions */}
                            <div className="border-t border-gray-100 pt-4 flex justify-end space-x-3">
                                <button
                                    type="button"
                                    onClick={() => setShowEditLogModal(false)}
                                    className="px-4 py-2 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl font-semibold text-slate-500 transition"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={editLogForm.processing}
                                    className="px-4 py-2 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl font-semibold transition"
                                >
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}

            {/* Modal: Riwayat Pengecekan (History Table) */}
            {showHistoryModal && (
                <div className="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4 overflow-y-auto">
                    <div className="bg-white rounded-3xl w-full max-w-5xl p-6 space-y-6 shadow-2xl relative">
                        <div className="flex justify-between items-start border-b border-gray-100 pb-3">
                            <div>
                                <h3 className="font-extrabold text-lg text-indigo-950">Riwayat Log Pengecekan PM</h3>
                                <p className="text-xs text-gray-500 mt-0.5">Daftar lengkap pemeriksaan fungsi sarana rumah sakit</p>
                            </div>
                            <button onClick={() => setShowHistoryModal(false)} className="text-gray-400 hover:text-gray-600 text-xl font-bold">×</button>
                        </div>

                        <div className="overflow-x-auto border border-slate-100 rounded-2xl">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="bg-slate-50 border-b border-slate-100 text-xs font-bold text-slate-500 uppercase tracking-wider">
                                        <th className="px-6 py-4">Nama Item</th>
                                        <th className="px-6 py-4">Frekuensi</th>
                                        <th className="px-6 py-4">Pelaksana</th>
                                        <th className="px-6 py-4 text-center">Tgl Selesai</th>
                                        <th className="px-6 py-4">Kondisi Hasil</th>
                                        <th className="px-6 py-4">Laporan Cek</th>
                                        <th className="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-slate-100 text-sm">
                                    {completedLogs.length > 0 ? (
                                        completedLogs.map((log) => (
                                            <tr key={log.id_log} className="hover:bg-slate-50/50 transition">
                                                <td className="px-6 py-4 font-bold text-slate-800">{log.nama_item}</td>
                                                <td className="px-6 py-4 text-xs font-extrabold text-slate-500 uppercase">{log.frekuensi}</td>
                                                <td className="px-6 py-4 text-slate-600 font-semibold">{log.pelaksana}</td>
                                                <td className="px-6 py-4 text-center text-xs text-slate-450 font-bold">{log.tgl_pelaksanaan}</td>
                                                <td className="px-6 py-4">
                                                    <span className={`inline-flex px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider ${
                                                        log.kondisi_laporan === 'Normal' ? 'bg-emerald-50 text-emerald-700' :
                                                        log.kondisi_laporan === 'Butuh Perbaikan' ? 'bg-amber-50 text-amber-700' : 'bg-rose-50 text-rose-700'
                                                    }`}>
                                                        {log.kondisi_laporan}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 text-slate-500 max-w-[200px] truncate" title={log.hasil_pengecekan}>
                                                    {log.hasil_pengecekan}
                                                </td>
                                                <td className="px-6 py-4 text-right space-x-2 whitespace-nowrap">
                                                    <button
                                                        onClick={() => {
                                                            setShowHistoryModal(false);
                                                            openEditLogModal(log);
                                                        }}
                                                        className="px-2 py-1 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-lg shadow-sm"
                                                    >
                                                        Edit
                                                    </button>
                                                    <button
                                                        onClick={() => handleDeleteLog(log.id_log)}
                                                        className="px-2 py-1 bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-150 text-rose-600 text-xs font-semibold rounded-lg shadow-sm"
                                                    >
                                                        Hapus
                                                    </button>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="7" className="text-center py-12 text-slate-400 font-semibold">
                                                Belum ada riwayat pemeriksaan selesai.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
