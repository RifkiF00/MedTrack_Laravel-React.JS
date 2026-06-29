import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Create({ auth, asets, defaultPelapor }) {
    const { data, setData, post, processing, errors } = useForm({
        id_aset: '',
        tingkat_urgensi: 'Sedang',
        nama_pelapor_bebas: defaultPelapor || '',
        deskripsi_kerusakan: '',
        foto_kerusakan: null
    });

    const [imagePreview, setImagePreview] = useState(null);

    const handleFileChange = (e) => {
        const file = e.target.files[0];
        setData('foto_kerusakan', file);

        if (file) {
            const reader = new FileReader();
            reader.onloadend = () => {
                setImagePreview(reader.result);
            };
            reader.readAsDataURL(file);
        } else {
            setImagePreview(null);
        }
    };

    const submit = (e) => {
        e.preventDefault();
        // Gunakan post dengan forceFormData: true karena form mengunggah file
        post(route('workorder.store'), {
            forceFormData: true,
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-semibold text-xl text-gray-800 leading-tight">Buat Work Order Baru</h2>
                        <p className="text-sm text-gray-500 mt-1">Laporkan kerusakan aset ruangan Anda untuk ditangani unit IPSRS</p>
                    </div>
                    <Link
                        href={route('workorder.index')}
                        className="px-4 py-2 bg-gray-50 border border-gray-200 hover:bg-gray-100 text-gray-700 rounded-xl text-sm font-semibold transition"
                    >
                        Kembali ke List
                    </Link>
                </div>
            }
        >
            <Head title="Buat Work Order" />

            <div className="py-10">
                <div className="max-w-3xl mx-auto sm:px-6 lg:px-8">
                    <form onSubmit={submit} className="bg-white border border-gray-100 shadow-sm rounded-3xl overflow-hidden p-8 space-y-6">
                        
                        <div className="border-b border-gray-100 pb-4">
                            <h3 className="font-bold text-lg text-gray-900">Form Laporan Kerusakan</h3>
                            <p className="text-xs text-gray-500">Mohon lengkapi detail kerusakan berikut agar penanganan lebih cepat dan tepat.</p>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            {/* Pilih Aset Ruangan */}
                            <div className="md:col-span-2">
                                <label className="block text-sm font-semibold text-gray-700">Pilih Aset Bermasalah *</label>
                                <select
                                    required
                                    value={data.id_aset}
                                    onChange={(e) => setData('id_aset', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="">-- Pilih Aset Ruangan Anda --</option>
                                    {asets.map((a) => (
                                        <option key={a.id_aset} value={a.id_aset}>
                                            {a.kode_label} - {a.nama_alat}
                                        </option>
                                    ))}
                                </select>
                                {errors.id_aset && <p className="text-rose-500 text-xs mt-1">{errors.id_aset}</p>}
                            </div>

                            {/* Tingkat Urgensi */}
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Tingkat Urgensi *</label>
                                <select
                                    value={data.tingkat_urgensi}
                                    onChange={(e) => setData('tingkat_urgensi', e.target.value)}
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                >
                                    <option value="Rendah">Rendah (Dapat menunggu, fungsi normal)</option>
                                    <option value="Sedang">Sedang (Kendala fungsional minor)</option>
                                    <option value="Tinggi">Tinggi (Aktivitas unit terganggu)</option>
                                    <option value="Darurat">Darurat (Membahayakan keselamatan/statis total)</option>
                                </select>
                                {errors.tingkat_urgensi && <p className="text-rose-500 text-xs mt-1">{errors.tingkat_urgensi}</p>}
                            </div>

                            {/* Nama Pelapor Bebas */}
                            <div>
                                <label className="block text-sm font-semibold text-gray-700">Nama Pelapor / Kontak *</label>
                                <input
                                    type="text"
                                    required
                                    value={data.nama_pelapor_bebas}
                                    onChange={(e) => setData('nama_pelapor_bebas', e.target.value)}
                                    placeholder="Contoh: Suster Siska / Dr. Andi"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {errors.nama_pelapor_bebas && <p className="text-rose-500 text-xs mt-1">{errors.nama_pelapor_bebas}</p>}
                                <small className="text-[10px] text-gray-400 mt-1 block">Tulis nama penanggung jawab atau kontak pelapor lapangan.</small>
                            </div>

                            {/* Deskripsi Kerusakan */}
                            <div className="md:col-span-2">
                                <label className="block text-sm font-semibold text-gray-700">Deskripsi Kerusakan / Kendala Alat *</label>
                                <textarea
                                    required
                                    value={data.deskripsi_kerusakan}
                                    onChange={(e) => setData('deskripsi_kerusakan', e.target.value)}
                                    placeholder="Jelaskan kronologi kerusakan, gejala error, atau kondisi fisik alat secara rinci..."
                                    rows="4"
                                    className="mt-1.5 w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500"
                                />
                                {errors.deskripsi_kerusakan && <p className="text-rose-500 text-xs mt-1">{errors.deskripsi_kerusakan}</p>}
                            </div>

                            {/* Foto Kerusakan */}
                            <div className="md:col-span-2 space-y-3">
                                <label className="block text-sm font-semibold text-gray-700">Unggah Foto Bukti Kerusakan</label>
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleFileChange}
                                    className="w-full border border-gray-200 bg-gray-50 rounded-xl px-4 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 file:mr-4 file:py-1 file:px-3 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                {errors.foto_kerusakan && <p className="text-rose-500 text-xs mt-1">{errors.foto_kerusakan}</p>}
                                
                                {imagePreview && (
                                    <div className="mt-2">
                                        <p className="text-xs text-gray-400 mb-1 font-semibold">Pratinjau Foto:</p>
                                        <img 
                                            src={imagePreview} 
                                            alt="Pratinjau Bukti" 
                                            className="max-h-64 object-cover rounded-2xl border border-gray-200 shadow-sm"
                                        />
                                    </div>
                                )}
                            </div>
                        </div>

                        {/* Submit Buttons */}
                        <div className="border-t border-gray-100 pt-6 flex justify-end space-x-3">
                            <Link
                                href={route('workorder.index')}
                                className="px-5 py-2.5 bg-gray-50 hover:bg-gray-100 border border-gray-200 rounded-xl text-sm font-semibold text-gray-700 transition"
                            >
                                Batal
                            </Link>
                            <button
                                type="submit"
                                disabled={processing}
                                className="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl text-sm font-semibold transition shadow-sm"
                            >
                                Kirim Laporan (Buat WO)
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
