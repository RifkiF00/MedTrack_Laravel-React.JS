import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Create({ auth, asets, ruangans }) {
    const { data, setData, post, processing, errors } = useForm({
        id_aset: '',
        ruang_asal: '',
        ruang_tujuan: '',
        alasan_mutasi: '',
        catatan: ''
    });

    const [selectedAset, setSelectedAset] = useState(null);

    const handleAsetChange = (asetId) => {
        const aset = asets.find(a => a.id_aset === parseInt(asetId));
        if (aset) {
            setSelectedAset(aset);
            setData(prev => ({
                ...prev,
                id_aset: asetId,
                ruang_asal: aset.id_ruang_saat_ini
            }));
        } else {
            setSelectedAset(null);
            setData(prev => ({
                ...prev,
                id_aset: '',
                ruang_asal: ''
            }));
        }
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('mutasi.store'));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-semibold text-xl text-gray-800 leading-tight">Catat Perpindahan Aset</h2>
                        <p className="text-sm text-gray-500 mt-1">Ajukan permohonan mutasi inventaris barang medis antar ruangan</p>
                    </div>
                    <Link
                        href={route('mutasi.index')}
                        className="px-4 py-2 bg-gray-50 border border-gray-200 hover:bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold transition"
                    >
                        Batal
                    </Link>
                </div>
            }
        >
            <Head title="Catat Mutasi Ruangan" />

            <div className="py-10">
                <div className="max-w-3xl mx-auto sm:px-6 lg:px-8">
                    <form onSubmit={submit} className="bg-white border border-gray-100 shadow-sm rounded-2xl p-8 space-y-6">
                        
                        <div className="space-y-4">
                            {/* Aset Medis */}
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Pilih Aset Medis *</label>
                                <select
                                    value={data.id_aset}
                                    onChange={(e) => handleAsetChange(e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">-- Pilih Alat Medis --</option>
                                    {asets.map((a) => (
                                        <option key={a.id_aset} value={a.id_aset}>
                                            [{a.kode_label}] {a.nama_alat} (Saat ini: {a.nama_ruang})
                                        </option>
                                    ))}
                                </select>
                                {errors.id_aset && <p className="text-rose-500 text-xs mt-1">{errors.id_aset}</p>}
                            </div>

                            <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {/* Ruangan Asal (Readonly) */}
                                <div>
                                    <label className="block text-sm font-semibold text-gray-400">Ruangan Asal (Terkunci)</label>
                                    <input
                                        type="text"
                                        readOnly
                                        value={selectedAset ? selectedAset.nama_ruang : 'Pilih aset terlebih dahulu...'}
                                        className="mt-1.5 w-full border border-gray-150 bg-gray-100 text-gray-500 rounded-xl px-4 py-2.5 text-sm focus:outline-none cursor-not-allowed"
                                    />
                                    {errors.ruang_asal && <p className="text-rose-500 text-xs mt-1">{errors.ruang_asal}</p>}
                                </div>

                                {/* Ruangan Tujuan */}
                                <div>
                                    <label className="block text-sm font-semibold text-gray-700">Ruangan Tujuan Mutasi *</label>
                                    <select
                                        value={data.ruang_tujuan}
                                        onChange={(e) => setData('ruang_tujuan', e.target.value)}
                                        className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option value="">-- Pilih Ruangan Baru --</option>
                                        {ruangans
                                            .filter(r => !selectedAset || r.id_ruang !== selectedAset.id_ruang_saat_ini)
                                            .map((r) => (
                                                <option key={r.id_ruang} value={r.id_ruang}>{r.nama_ruang}</option>
                                            ))}
                                    </select>
                                    {errors.ruang_tujuan && <p className="text-rose-500 text-xs mt-1">{errors.ruang_tujuan}</p>}
                                </div>
                            </div>

                            {/* Alasan Mutasi */}
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Alasan Pemindahan *</label>
                                <input
                                    type="text"
                                    value={data.alasan_mutasi}
                                    onChange={(e) => setData('alasan_mutasi', e.target.value)}
                                    placeholder="Contoh: Kalibrasi ulang di laboratorium / Peminjaman alat medis darurat"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {errors.alasan_mutasi && <p className="text-rose-500 text-xs mt-1">{errors.alasan_mutasi}</p>}
                            </div>

                            {/* Catatan Tambahan */}
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Catatan Tambahan (Opsional)</label>
                                <textarea
                                    value={data.catatan}
                                    onChange={(e) => setData('catatan', e.target.value)}
                                    rows="4"
                                    placeholder="Masukkan detail tambahan tentang kondisi fisik alat atau instruksi penanganan..."
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {errors.catatan && <p className="text-rose-500 text-xs mt-1">{errors.catatan}</p>}
                            </div>
                        </div>

                        {/* Submit Actions */}
                        <div className="border-t border-gray-100 pt-6 flex justify-end space-x-3">
                            <Link
                                href={route('mutasi.index')}
                                className="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl text-sm font-semibold transition shadow-sm"
                            >
                                Simpan Pengajuan Mutasi
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
