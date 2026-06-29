import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Create({ auth, asets }) {
    const { data, setData, post, processing, errors } = useForm({
        nama_item: '',
        deskripsi: '',
        lokasi: '',
        frekuensi: 'Bulanan',
        catatan: '',
        tgl_rencana_awal: ''
    });

    const [selectedAsetId, setSelectedAsetId] = useState('');

    const handleAsetChange = (asetId) => {
        setSelectedAsetId(asetId);
        const selected = asets.find(a => a.id_aset == asetId);
        if (selected) {
            setData(prev => ({
                ...prev,
                nama_item: selected.nama_alat,
                lokasi: selected.lokasi
            }));
        } else {
            setData(prev => ({
                ...prev,
                nama_item: '',
                lokasi: ''
            }));
        }
    };

    const submit = (e) => {
        e.preventDefault();
        post(route('maintenance.store'));
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-semibold text-xl text-gray-800 leading-tight">Tambah Jadwal Pemeliharaan</h2>
                        <p className="text-sm text-gray-500 mt-1">Buat master jadwal preventive maintenance baru untuk sarana/prasarana rumah sakit</p>
                    </div>
                    <Link
                        href={route('maintenance.index')}
                        className="px-4 py-2 bg-gray-50 border border-gray-200 hover:bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold transition"
                    >
                        ← Kembali
                    </Link>
                </div>
            }
        >
            <Head title="Tambah Jadwal Maintenance" />

            <div className="py-10">
                <div className="max-w-3xl mx-auto sm:px-6 lg:px-8">
                    <form onSubmit={submit} className="bg-white border border-gray-100 shadow-sm rounded-3xl overflow-hidden p-8 space-y-6">
                        <div className="border-b border-gray-100 pb-4">
                            <h3 className="font-bold text-lg text-gray-900">Form Pemeliharaan Rutin</h3>
                            <p className="text-xs text-gray-500">Tentukan alat medis atau sarana yang akan diperiksa secara berkala.</p>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {/* Pilih Aset */}
                            <div className="md:col-span-2">
                                <label className="block text-sm font-semibold text-gray-700">Pilih Aset Terdaftar *</label>
                                <select
                                    value={selectedAsetId}
                                    onChange={(e) => handleAsetChange(e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">-- Pilih Aset --</option>
                                    {asets.map((a) => (
                                        <option key={a.id_aset} value={a.id_aset}>
                                            {a.kode_label} - {a.nama_alat} ({a.lokasi})
                                        </option>
                                    ))}
                                </select>
                            </div>

                            {/* Nama Item (Manual/Auto) */}
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Nama Item Pemeliharaan *</label>
                                <input
                                    type="text"
                                    required
                                    value={data.nama_item}
                                    onChange={(e) => setData('nama_item', e.target.value)}
                                    placeholder="Contoh: Defibrillator ICU"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {errors.nama_item && <p className="text-rose-500 text-xs mt-1">{errors.nama_item}</p>}
                            </div>

                            {/* Lokasi (Auto) */}
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Lokasi / Ruangan *</label>
                                <input
                                    type="text"
                                    required
                                    value={data.lokasi}
                                    onChange={(e) => setData('lokasi', e.target.value)}
                                    placeholder="Contoh: Ruang ICU Lantai 2"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {errors.lokasi && <p className="text-rose-500 text-xs mt-1">{errors.lokasi}</p>}
                            </div>

                            {/* Frekuensi */}
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Frekuensi Pemeliharaan *</label>
                                <select
                                    value={data.frekuensi}
                                    onChange={(e) => setData('frekuensi', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="Harian">Harian</option>
                                    <option value="2x_Harian">2x Harian</option>
                                    <option value="3x_Harian">3x Harian</option>
                                    <option value="Mingguan">Mingguan</option>
                                    <option value="Bulanan">Bulanan</option>
                                    <option value="3_Bulanan">3 Bulanan</option>
                                    <option value="6_Bulanan">6 Bulanan</option>
                                    <option value="Tahunan">Tahunan</option>
                                </select>
                                {errors.frekuensi && <p className="text-rose-500 text-xs mt-1">{errors.frekuensi}</p>}
                            </div>

                            {/* Tanggal Rencana Awal */}
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Tanggal Rencana Mulai *</label>
                                <input
                                    type="date"
                                    required
                                    value={data.tgl_rencana_awal}
                                    onChange={(e) => setData('tgl_rencana_awal', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {errors.tgl_rencana_awal && <p className="text-rose-500 text-xs mt-1">{errors.tgl_rencana_awal}</p>}
                            </div>

                            {/* Deskripsi */}
                            <div className="md:col-span-2">
                                <label className="block text-sm font-semibold text-gray-700">Deskripsi Pemeliharaan</label>
                                <textarea
                                    value={data.deskripsi}
                                    onChange={(e) => setData('deskripsi', e.target.value)}
                                    placeholder="Jelaskan detail pengecekan standar (cth: Cek baterai backup, bersihkan probe, kalibrasi fungsional)..."
                                    rows="3"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {errors.deskripsi && <p className="text-rose-500 text-xs mt-1">{errors.deskripsi}</p>}
                            </div>

                            {/* Catatan Tambahan */}
                            <div className="md:col-span-2">
                                <label className="block text-sm font-semibold text-gray-700">Catatan Khusus (Opsional)</label>
                                <input
                                    type="text"
                                    value={data.catatan}
                                    onChange={(e) => setData('catatan', e.target.value)}
                                    placeholder="Instruksi tambahan jika ada..."
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {errors.catatan && <p className="text-rose-500 text-xs mt-1">{errors.catatan}</p>}
                            </div>
                        </div>

                        {/* Actions */}
                        <div className="border-t border-gray-100 pt-6 flex justify-end space-x-3">
                            <Link
                                href={route('maintenance.index')}
                                className="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl text-sm font-semibold transition shadow-sm"
                            >
                                Simpan Jadwal Pemeliharaan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
