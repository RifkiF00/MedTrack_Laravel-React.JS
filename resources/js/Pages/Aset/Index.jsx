import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Index({ auth, asets, filters }) {
    const [search, setSearch] = useState(filters.search || '');
    const [kategori, setKategori] = useState(filters.kategori || '');
    const [kondisi, setKondisi] = useState(filters.kondisi || '');

    const handleFilter = () => {
        router.get(route('aset.index'), { search, kategori, kondisi }, { preserveState: true });
    };

    const handleReset = () => {
        setSearch('');
        setKategori('');
        setKondisi('');
        router.get(route('aset.index'));
    };

    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus aset medis ini? File gambar dan sticker QR Code terkait akan ikut dihapus secara permanen.')) {
            router.delete(route('aset.destroy', id));
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center flex-wrap gap-4">
                    <div>
                        <h2 className="font-extrabold text-2xl text-slate-800 leading-tight">Master Inventaris Aset</h2>
                        <p className="text-xs text-slate-500 mt-1">Kelola informasi peralatan medis dan kelayakan operasional</p>
                    </div>
                    {/* Header Buttons */}
                    <div className="flex items-center space-x-3">
                        <Link
                            href={route('tracking.map')}
                            className="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-sm font-semibold transition shadow-sm flex items-center"
                        >
                            Peta Tracking Aset
                        </Link>
                        <Link
                            href={route('aset.create')}
                            className="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-sm font-semibold transition shadow-sm"
                        >
                            + Tambah Aset Baru
                        </Link>
                    </div>
                </div>
            }
        >
            <Head title="Master Aset" />

            <div className="space-y-6 pb-8">
                {/* Filter Panel */}
                <div className="bg-white p-5 rounded-2xl border border-slate-100 shadow-sm grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    <div>
                        <label className="text-xs font-bold text-slate-400 uppercase tracking-wider">Cari Alat / Kode</label>
                        <input
                            type="text"
                            value={search}
                            onChange={(e) => setSearch(e.target.value)}
                            placeholder="Masukkan nama alat / label..."
                            className="mt-1.5 w-full bg-slate-50 border-0 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        />
                    </div>

                    <div>
                        <label className="text-xs font-bold text-slate-400 uppercase tracking-wider">Kategori</label>
                        <select
                            value={kategori}
                            onChange={(e) => setKategori(e.target.value)}
                            className="mt-1.5 w-full bg-slate-50 border-0 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="">Semua Kategori</option>
                            <option value="Medis">Medis</option>
                            <option value="Sarpras">Sarpras</option>
                            <option value="IT">IT</option>
                        </select>
                    </div>

                    <div>
                        <label className="text-xs font-bold text-slate-400 uppercase tracking-wider">Kondisi</label>
                        <select
                            value={kondisi}
                            onChange={(e) => setKondisi(e.target.value)}
                            className="mt-1.5 w-full bg-slate-50 border-0 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                        >
                            <option value="">Semua Kondisi</option>
                            <option value="Baik">Baik</option>
                            <option value="Rusak_Ringan">Rusak Ringan</option>
                            <option value="Rusak_Berat">Rusak Berat</option>
                            <option value="Maintenance">Maintenance</option>
                            <option value="Gudang">Di Gudang</option>
                        </select>
                    </div>

                    <div className="flex space-x-2">
                        <button
                            onClick={handleFilter}
                            className="flex-1 py-2.5 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 rounded-xl text-sm font-semibold transition"
                        >
                            Terapkan Filter
                        </button>
                        <button
                            onClick={handleReset}
                            className="px-3 py-2.5 bg-slate-50 text-slate-600 hover:bg-slate-100 rounded-xl text-sm font-medium transition"
                        >
                            Reset
                        </button>
                    </div>
                </div>

                {/* Table View */}
                <div className="bg-white rounded-2xl border border-slate-100 shadow-sm overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="bg-slate-50/75 border-b border-slate-100 text-xs font-bold text-slate-450 uppercase tracking-wider">
                                    <th className="px-6 py-4">Kode Label</th>
                                    <th className="px-6 py-4">Nama Alat</th>
                                    <th className="px-6 py-4">Kategori</th>
                                    <th className="px-6 py-4">Kondisi</th>
                                    <th className="px-6 py-4">Ruangan</th>
                                    <th className="px-6 py-4 text-center">QR Code</th>
                                    <th className="px-6 py-4 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody className="divide-y divide-slate-100">
                                {asets.data.length > 0 ? (
                                    asets.data.map((aset) => (
                                        <tr key={aset.id_aset} className="hover:bg-slate-50/50 transition">
                                            <td className="px-6 py-4 font-mono text-xs font-bold text-slate-700">{aset.kode_label}</td>
                                            <td className="px-6 py-4 font-bold text-slate-900">{aset.nama_alat}</td>
                                            <td className="px-6 py-4 text-sm text-slate-600">{aset.kategori_aset}</td>
                                            <td className="px-6 py-4">
                                                <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-extrabold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200/50">
                                                    <span className={`w-1.5 h-1.5 rounded-full mr-1.5 ${
                                                        aset.status_kondisi === 'Baik' ? 'bg-emerald-500' :
                                                        aset.status_kondisi === 'Maintenance' ? 'bg-blue-500' :
                                                        aset.status_kondisi === 'Rusak_Berat' ? 'bg-rose-500' : 'bg-amber-500'
                                                    }`}></span>
                                                    {aset.status_kondisi.replace('_', ' ')}
                                                </span>
                                            </td>
                                            <td className="px-6 py-4 text-sm text-slate-600">{aset.ruangan ? aset.ruangan.nama_ruang : 'Tidak ada'}</td>
                                            <td className="px-6 py-4 text-center">
                                                {aset.file_qr_code ? (
                                                    <a
                                                        href={`/uploads/qr/${aset.file_qr_code}`}
                                                        download
                                                        className="inline-flex text-slate-500 hover:text-indigo-600 hover:underline text-xs font-semibold transition"
                                                    >
                                                        Download QR
                                                    </a>
                                                ) : (
                                                    <span className="text-xs text-slate-400">Belum tersedia</span>
                                                )}
                                            </td>
                                            <td className="px-6 py-4 text-right">
                                                <div className="flex justify-end items-center space-x-2">
                                                    <Link
                                                        href={route('aset.show', aset.id_aset)}
                                                        className="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-semibold transition shadow-sm"
                                                    >
                                                        Detail
                                                    </Link>
                                                    <Link
                                                        href={route('aset.edit', aset.id_aset)}
                                                        className="px-3 py-1.5 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-lg text-xs font-semibold transition shadow-sm"
                                                    >
                                                        Edit
                                                    </Link>
                                                    <button
                                                        onClick={() => handleDelete(aset.id_aset)}
                                                        className="px-3 py-1.5 bg-white border border-slate-200 hover:bg-rose-50 hover:border-rose-200 text-rose-600 rounded-lg text-xs font-semibold transition shadow-sm"
                                                    >
                                                        Hapus
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                ) : (
                                    <tr>
                                        <td colSpan="7" className="text-center py-12 text-slate-400">
                                            Tidak ada data aset medis yang ditemukan.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>

                    {/* Simple Pagination */}
                    {asets.links && asets.links.length > 3 && (
                        <div className="bg-slate-50 px-6 py-4 border-t border-slate-100 flex items-center justify-between">
                            <span className="text-xs text-slate-500">
                                Menampilkan {asets.from || 0} - {asets.to || 0} dari {asets.total} data
                            </span>
                            <div className="flex space-x-1">
                                {asets.links.map((link, i) => (
                                    <button
                                        key={i}
                                        disabled={!link.url || link.active}
                                        onClick={() => router.get(link.url, { search, kategori, kondisi }, { preserveState: true })}
                                        className={`px-3 py-1 text-xs font-semibold rounded-lg transition ${
                                            link.active ? 'bg-[#0a3a60] text-white' :
                                            !link.url ? 'text-slate-350 cursor-not-allowed' : 'bg-white hover:bg-slate-100 text-slate-700 border border-slate-200'
                                        }`}
                                        dangerouslySetInnerHTML={{ __html: link.label }}
                                    />
                                ))}
                            </div>
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
