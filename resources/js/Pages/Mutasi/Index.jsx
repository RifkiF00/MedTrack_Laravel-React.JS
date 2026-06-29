import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';

export default function Index({ auth, mutasiList, role, stats }) {
    
    const handleApprove = (id) => {
        if (confirm('Apakah Anda yakin ingin menyetujui mutasi ini?')) {
            router.post(route('mutasi.approve', id));
        }
    };

    const handleReject = (id) => {
        if (confirm('Apakah Anda yakin ingin menolak mutasi ini?')) {
            router.post(route('mutasi.reject', id));
        }
    };

    const handleComplete = (id) => {
        if (confirm('Apakah Anda yakin mutasi ini telah selesai dilakukan secara fisik?')) {
            router.post(route('mutasi.complete', id));
        }
    };

    const isIPSRS = role === 'Admin_IPSRS' || role === 'Staf_IPSRS';

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-bold text-xl text-slate-800 leading-tight">Mutasi Penempatan Ruangan</h2>
                        <p className="text-xs text-slate-500 mt-1">Daftar pencatatan perpindahan alat medis antar ruang perawatan</p>
                    </div>
                    <Link
                        href={route('mutasi.create')}
                        className="px-4 py-2 bg-[#0a3a60] hover:bg-[#0c4775] text-white rounded-xl text-xs font-bold transition shadow-sm"
                    >
                        + Catat Mutasi Baru
                    </Link>
                </div>
            }
        >
            <Head title="Mutasi Ruangan" />

            <div className="space-y-6 pb-8">
                
                {/* Stats Grid */}
                <div className="grid grid-cols-2 md:grid-cols-4 gap-6">
                    {/* Pending */}
                    <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-slate-200 transition duration-150">
                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Menunggu Verifikasi</span>
                        <span className="text-2xl font-bold text-slate-800 mt-2">{stats.pending}</span>
                    </div>

                    {/* Approved */}
                    <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-slate-200 transition duration-150">
                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Disetujui</span>
                        <span className="text-2xl font-bold text-slate-800 mt-2">{stats.approved}</span>
                    </div>

                    {/* Completed */}
                    <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-slate-200 transition duration-150">
                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Selesai Mutasi</span>
                        <span className="text-2xl font-bold text-slate-800 mt-2">{stats.completed}</span>
                    </div>

                    {/* Rejected */}
                    <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm flex flex-col justify-between hover:border-slate-200 transition duration-150">
                        <span className="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Ditolak</span>
                        <span className="text-2xl font-bold text-slate-800 mt-2">{stats.rejected}</span>
                    </div>
                </div>

                {/* Table Mutations */}
                <div className="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="bg-slate-55 bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-500 uppercase tracking-wider">
                                    <th className="px-6 py-4">Aset Medis</th>
                                    <th className="px-6 py-4">Alur Perpindahan Ruangan</th>
                                    <th className="px-6 py-4">Alasan Mutasi</th>
                                    <th className="px-6 py-4 text-center">Tgl Pengajuan</th>
                                    <th className="px-6 py-4 text-center">Status</th>
                                    <th className="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100 text-xs">
                                {mutasiList.length > 0 ? (
                                    mutasiList.map((m) => (
                                        <tr key={m.id_mutasi} className="hover:bg-slate-50/50 transition">
                                            <td className="px-6 py-4">
                                                <div className="font-semibold text-slate-800 text-sm">{m.nama_alat}</div>
                                                <div className="font-mono text-[10px] text-slate-400 mt-0.5">{m.kode_label}</div>
                                            </td>
                                            <td className="px-6 py-4">
                                                <div className="flex items-center text-slate-600 font-medium">
                                                    <span>{m.ruang_asal}</span>
                                                    <span className="text-slate-400 mx-2.5">➔</span>
                                                    <span className="text-slate-800 font-semibold">{m.ruang_tujuan}</span>
                                                </div>
                                            </td>
                                            <td className="px-6 py-4 text-slate-600 max-w-[200px]" title={m.alasan_mutasi}>
                                                <p className="truncate">{m.alasan_mutasi || '-'}</p>
                                            </td>
                                            <td className="px-6 py-4 text-center text-slate-500 font-medium whitespace-nowrap">{m.tgl_mutasi || '-'}</td>
                                            <td className="px-6 py-4 text-center">
                                                <span className={`inline-flex px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider ${
                                                    m.status_mutasi === 'Menunggu_Verifikasi' ? 'bg-amber-50 text-amber-700' :
                                                    m.status_mutasi === 'Disetujui' ? 'bg-blue-50 text-blue-700' :
                                                    m.status_mutasi === 'Selesai' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700'
                                                }`}>
                                                    {m.status_mutasi.replace('_', ' ')}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <div className="flex justify-end space-x-1.5">
                                                    {/* IPSRS Action buttons */}
                                                    {isIPSRS && m.status_mutasi === 'Menunggu_Verifikasi' && (
                                                        <>
                                                            <button
                                                                type="button"
                                                                onClick={() => handleApprove(m.id_mutasi)}
                                                                className="px-3 py-1 bg-emerald-600 hover:bg-emerald-700 text-white text-[10px] font-bold rounded-lg transition shadow-sm"
                                                            >
                                                                Setujui
                                                            </button>
                                                            <button
                                                                type="button"
                                                                onClick={() => handleReject(m.id_mutasi)}
                                                                className="px-3 py-1 bg-rose-600 hover:bg-rose-700 text-white text-[10px] font-bold rounded-lg transition shadow-sm"
                                                            >
                                                                Tolak
                                                            </button>
                                                        </>
                                                    )}

                                                    {isIPSRS && m.status_mutasi === 'Disetujui' && (
                                                        <button
                                                            type="button"
                                                            onClick={() => handleComplete(m.id_mutasi)}
                                                            className="px-3.5 py-1.5 bg-[#0a3a60] hover:bg-[#0c4775] text-white text-[10px] font-bold rounded-lg transition shadow-sm"
                                                        >
                                                            Selesaikan
                                                        </button>
                                                    )}

                                                    {/* Guest / default state */}
                                                    {!isIPSRS && m.status_mutasi === 'Menunggu_Verifikasi' && (
                                                        <span className="text-slate-400 italic text-[11px]">Menunggu Review</span>
                                                    )}
                                                    {m.status_mutasi === 'Selesai' && (
                                                        <span className="text-emerald-600 font-bold text-[11px]">Selesai</span>
                                                    )}
                                                    {m.status_mutasi === 'Ditolak' && (
                                                        <span className="text-rose-600 font-bold text-[11px]">Ditolak</span>
                                                    )}
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="6" className="text-center py-16 text-slate-400">
                                            <p className="font-medium text-sm">Belum ada data permintaan mutasi ruangan.</p>
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
