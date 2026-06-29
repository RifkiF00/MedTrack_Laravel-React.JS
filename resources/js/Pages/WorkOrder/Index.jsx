import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Index({ auth, workorders, teknisiList, role, flash }) {
    const isIPSRS = ['Admin_IPSRS', 'Staf_IPSRS'].includes(role);
    const isUnit = role === 'Unit_RS';

    // State mappings for row-specific inline inputs
    const [selectedTeknisi, setSelectedTeknisi] = useState({});
    const [selectedStatus, setSelectedStatus] = useState({});
    const [catatanStatus, setCatatanStatus] = useState({});
    const [catatanSignOff, setCatatanSignOff] = useState({});

    // Inline Actions Submit handlers
    const handleAssign = (id_ticket, defaultTeknisiId) => {
        const idTeknisi = selectedTeknisi[id_ticket] !== undefined ? selectedTeknisi[id_ticket] : defaultTeknisiId;
        if (!idTeknisi) {
            alert('Pilih teknisi terlebih dahulu.');
            return;
        }
        router.post(route('workorder.assign', id_ticket), {
            id_teknisi_penanggungjawab: idTeknisi
        });
    };

    const handleUpdateStatus = (id_ticket, currentStatus) => {
        const statusVal = selectedStatus[id_ticket] || currentStatus;
        const catatanVal = catatanStatus[id_ticket] || '';
        router.post(route('workorder.status', id_ticket), {
            status_ticket: statusVal,
            catatan_status: catatanVal
        });
    };

    const handleSignOff = (id_ticket) => {
        if (confirm('Apakah Anda yakin pekerjaan pemeliharaan ini telah diselesaikan dengan baik?')) {
            const notes = catatanSignOff[id_ticket] || 'Sign-Off Verifikasi Selesai oleh Unit';
            router.post(route('workorder.signoff', id_ticket), {
                catatan_signoff: notes
            });
        }
    };

    // Urgency badges
    const getUrgencyBadge = (urgency) => {
        const styles = {
            Rendah: 'bg-sky-50 border-sky-100 text-sky-700',
            Sedang: 'bg-amber-50 border-amber-100 text-amber-700',
            Tinggi: 'bg-rose-50 border-rose-100 text-rose-700',
            Darurat: 'bg-red-100 border-red-200 text-red-700 font-extrabold animate-pulse'
        };
        return (
            <span className={`inline-flex items-center px-2.5 py-1 border text-xs font-semibold rounded-lg uppercase tracking-wider ${styles[urgency] || 'bg-gray-50 text-gray-700'}`}>
                {urgency}
            </span>
        );
    };

    // Status badges
    const getStatusBadge = (status) => {
        const styles = {
            Open: 'bg-amber-50 border-amber-200 text-amber-700',
            Pengecekan: 'bg-indigo-50 border-indigo-200 text-indigo-700',
            Dikerjakan: 'bg-blue-50 border-blue-200 text-blue-700',
            'Menunggu Sign-Off': 'bg-purple-50 border-purple-200 text-purple-700 font-bold',
            Closed: 'bg-emerald-50 border-emerald-200 text-emerald-700'
        };
        return (
            <span className={`inline-flex items-center px-2.5 py-1 border text-xs font-semibold rounded-lg uppercase tracking-wider ${styles[status] || 'bg-gray-50 text-gray-700'}`}>
                {status}
            </span>
        );
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h2 className="font-semibold text-xl text-gray-800 leading-tight">E-Work Order</h2>
                        <p className="text-sm text-gray-500 mt-1">
                            {isIPSRS ? 'Daftar laporan kerusakan fasilitas rumah sakit' : 'Daftar laporan kerusakan unit Anda'}
                        </p>
                    </div>
                    {isUnit && (
                        <Link
                            href={route('workorder.create')}
                            className="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl text-sm font-semibold transition shadow-sm"
                        >
                            + Laporkan Kerusakan (Buat WO)
                        </Link>
                    )}
                </div>
            }
        >
            <Head title="Work Orders" />

            <div className="py-10">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

                    {/* Flash Alert */}
                    {flash && flash.success && (
                        <div className="p-4 bg-emerald-50 border border-emerald-100 text-emerald-800 rounded-2xl text-sm font-medium">
                            ✓ {flash.success}
                        </div>
                    )}
                    {flash && flash.error && (
                        <div className="p-4 bg-rose-50 border border-rose-100 text-rose-800 rounded-2xl text-sm font-medium">
                            ⚠️ {flash.error}
                        </div>
                    )}

                    <div className="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse min-w-[1000px]">
                                <thead>
                                    <tr className="bg-gray-50 border-b border-gray-100 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                                        <th className="px-6 py-4">No</th>
                                        <th className="px-6 py-4">Tgl Lapor</th>
                                        <th className="px-6 py-4">Kode Aset</th>
                                        <th className="px-6 py-4">Nama Alat</th>
                                        <th className="px-6 py-4">Lokasi / Ruang</th>
                                        <th className="px-6 py-4">Urgensi</th>
                                        <th className="px-6 py-4">Status</th>
                                        <th className="px-6 py-4">Pelapor</th>
                                        <th className="px-6 py-4">Teknisi PJ</th>
                                        <th className="px-6 py-4 text-center">Foto</th>
                                        <th className="px-6 py-4 text-right">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-100 text-sm">
                                    {workorders.length > 0 ? (
                                        workorders.map((wo, index) => (
                                            <tr key={wo.id_ticket} className="hover:bg-gray-50/50 transition">
                                                <td className="px-6 py-4 text-gray-400 font-semibold">{index + 1}</td>
                                                <td className="px-6 py-4 text-gray-500 text-xs font-semibold whitespace-nowrap">{wo.tgl_lapor}</td>
                                                <td className="px-6 py-4 font-mono text-xs text-indigo-700 font-semibold">{wo.kode_label}</td>
                                                <td className="px-6 py-4 font-semibold text-gray-900">{wo.nama_alat}</td>
                                                <td className="px-6 py-4 text-gray-600 font-medium">{wo.nama_ruang}</td>
                                                <td className="px-6 py-4">{getUrgencyBadge(wo.tingkat_urgensi)}</td>
                                                <td className="px-6 py-4">{getStatusBadge(wo.status_ticket)}</td>
                                                <td className="px-6 py-4 text-gray-600">{wo.nama_pelapor}</td>
                                                <td className="px-6 py-4 text-gray-600 font-semibold">{wo.nama_teknisi}</td>
                                                <td className="px-6 py-4 text-center">
                                                    {wo.foto_kerusakan ? (
                                                        <a 
                                                            href={`/uploads/troubleshoot/${wo.foto_kerusakan}`} 
                                                            target="_blank" 
                                                            rel="noopener noreferrer"
                                                            className="inline-block hover:scale-105 transition"
                                                        >
                                                            <img 
                                                                src={`/uploads/troubleshoot/${wo.foto_kerusakan}`}
                                                                alt="Bukti kerusakan"
                                                                className="w-12 h-12 object-cover rounded-xl border border-gray-200"
                                                            />
                                                        </a>
                                                    ) : (
                                                        <span className="text-gray-300">-</span>
                                                    )}
                                                </td>
                                                <td className="px-6 py-4 text-right">
                                                     {isIPSRS && (
                                                         <div className="flex flex-col space-y-3 w-[260px] text-left ml-auto">
                                                             {/* Assign Form */}
                                                             <div className="border border-slate-150 bg-slate-50/70 p-2.5 rounded-xl space-y-2">
                                                                 <label className="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Tugaskan Teknisi</label>
                                                                 <div className="flex gap-2">
                                                                     <select
                                                                         value={selectedTeknisi[wo.id_ticket] !== undefined ? selectedTeknisi[wo.id_ticket] : (wo.id_teknisi_penanggungjawab || '')}
                                                                         onChange={(e) => setSelectedTeknisi({
                                                                             ...selectedTeknisi,
                                                                             [wo.id_ticket]: e.target.value
                                                                         })}
                                                                         className="flex-1 border border-slate-250 bg-white rounded-lg px-2.5 py-1 text-xs focus:ring-1 focus:ring-indigo-500 focus:outline-none"
                                                                     >
                                                                         <option value="">-- Pilih Teknisi --</option>
                                                                         {teknisiList.map((t) => (
                                                                             <option key={t.id_user} value={t.id_user}>
                                                                                 {t.nama_lengkap}
                                                                             </option>
                                                                         ))}
                                                                     </select>
                                                                     <button
                                                                         onClick={() => handleAssign(wo.id_ticket, wo.id_teknisi_penanggungjawab)}
                                                                         className="px-3 py-1 bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white rounded-lg text-xs font-semibold transition shadow-sm"
                                                                     >
                                                                         Assign
                                                                     </button>
                                                                 </div>
                                                             </div>

                                                             {/* Status Form */}
                                                             <div className="border border-slate-150 bg-slate-50/70 p-2.5 rounded-xl space-y-2">
                                                                 <label className="block text-[10px] font-bold text-slate-500 uppercase tracking-wider">Update Progress</label>
                                                                 <div className="space-y-2">
                                                                     <div className="flex gap-2">
                                                                         <select
                                                                             value={selectedStatus[wo.id_ticket] || wo.status_ticket || ''}
                                                                             onChange={(e) => setSelectedStatus({
                                                                                 ...selectedStatus,
                                                                                 [wo.id_ticket]: e.target.value
                                                                             })}
                                                                             className="flex-1 border border-slate-250 bg-white rounded-lg px-2.5 py-1 text-xs focus:ring-1 focus:ring-sky-500 focus:outline-none"
                                                                         >
                                                                             <option value="Open">Open</option>
                                                                             <option value="Pengecekan">Pengecekan</option>
                                                                             <option value="Dikerjakan">Dikerjakan</option>
                                                                             <option value="Menunggu Sign-Off">Menunggu Sign-Off</option>
                                                                             <option value="Closed">Closed</option>
                                                                         </select>
                                                                         <button
                                                                             onClick={() => handleUpdateStatus(wo.id_ticket, wo.status_ticket)}
                                                                             className="px-3 py-1 bg-sky-600 hover:bg-sky-700 active:bg-sky-800 text-white rounded-lg text-xs font-semibold transition shadow-sm"
                                                                         >
                                                                             Update
                                                                         </button>
                                                                     </div>
                                                                     <input
                                                                         type="text"
                                                                         placeholder="Catatan tindak lanjut..."
                                                                         value={catatanStatus[wo.id_ticket] || ''}
                                                                         onChange={(e) => setCatatanStatus({
                                                                             ...catatanStatus,
                                                                             [wo.id_ticket]: e.target.value
                                                                         })}
                                                                         className="w-full border border-slate-250 bg-white rounded-lg px-2.5 py-1 text-xs focus:ring-1 focus:ring-sky-500 focus:outline-none"
                                                                     />
                                                                 </div>
                                                             </div>
                                                         </div>
                                                     )}
                                                     {isUnit && wo.status_ticket === 'Menunggu Sign-Off' && (
                                                         <div className="flex flex-col space-y-2 w-[260px] text-left ml-auto border border-emerald-150 bg-emerald-50/20 p-2.5 rounded-xl">
                                                             <label className="block text-[10px] font-bold text-emerald-700 uppercase tracking-wider">Verifikasi Pekerjaan</label>
                                                             <input
                                                                 type="text"
                                                                 placeholder="Catatan sign-off..."
                                                                 value={catatanSignOff[wo.id_ticket] || ''}
                                                                 onChange={(e) => setCatatanSignOff({
                                                                     ...catatanSignOff,
                                                                     [wo.id_ticket]: e.target.value
                                                                 })}
                                                                 className="w-full border border-emerald-250 bg-white rounded-lg px-2.5 py-1 text-xs focus:ring-1 focus:ring-emerald-500 focus:outline-none"
                                                             />
                                                             <button
                                                                 onClick={() => handleSignOff(wo.id_ticket)}
                                                                 className="w-full py-1.5 bg-emerald-600 hover:bg-emerald-700 active:bg-emerald-800 text-white rounded-lg text-xs font-semibold transition shadow-sm text-center"
                                                             >
                                                                 Sign-Off Selesai
                                                             </button>
                                                         </div>
                                                     )}
                                                     {((!isIPSRS && wo.status_ticket !== 'Menunggu Sign-Off') || (isUnit && wo.status_ticket !== 'Menunggu Sign-Off')) && (
                                                         <span className="text-gray-400 text-xs">-</span>
                                                     )}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="11" className="text-center py-16 text-gray-400">
                                                Belum ada Work Order (laporan kerusakan) yang terdaftar.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
