import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link } from '@inertiajs/react';
import { useEffect, useState } from 'react';

export default function Detail({ auth, aset, trackings, maintenances }) {
    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <div>
                        <h2 className="font-extrabold text-2xl text-slate-800 leading-tight">Detail Aset Medis</h2>
                        <p className="text-xs text-slate-500 mt-1">Detail informasi teknis, riwayat tracking, dan log pemeliharaan</p>
                    </div>
                    <Link
                        href={route('aset.index')}
                        className="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 rounded-xl text-sm font-semibold transition shadow-sm"
                    >
                        ← Kembali ke List
                    </Link>
                </div>
            }
        >
            <Head title={`Detail Aset - ${aset.nama_alat}`} />

            <div className="space-y-6 pb-8">
                
                {/* Main Specs & QR Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    
                    {/* Specs Card */}
                    <div className="lg:col-span-2 bg-white border border-slate-100 shadow-sm rounded-2xl p-6 space-y-6">
                        
                        <div className="flex items-center space-x-3.5 border-b border-slate-100 pb-4">
                            <div className="w-10 h-10 bg-indigo-50 flex items-center justify-center rounded-xl border border-indigo-100 text-indigo-500 font-bold text-lg">
                                🏥
                            </div>
                            <div>
                                <h3 className="font-bold text-lg text-slate-900">{aset.nama_alat}</h3>
                                <p className="font-mono text-xs font-semibold text-slate-500 mt-0.5">{aset.kode_label}</p>
                            </div>
                        </div>

                        <div className="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-sm">
                            <div>
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">Kategori</span>
                                <span className="font-semibold text-slate-800 mt-1 block">{aset.kategori_aset}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">Kondisi Alat</span>
                                <span className="mt-1 block">
                                    <span className="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200/50">
                                        <span className={`w-1.5 h-1.5 rounded-full mr-1.5 ${
                                            aset.status_kondisi === 'Baik' ? 'bg-emerald-500' :
                                            aset.status_kondisi === 'Maintenance' ? 'bg-blue-500' :
                                            aset.status_kondisi === 'Rusak_Berat' ? 'bg-rose-500' : 'bg-amber-500'
                                        }`}></span>
                                        {aset.status_kondisi.replace('_', ' ')}
                                    </span>
                                </span>
                            </div>
                            <div>
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">Merk / Brand</span>
                                <span className="font-semibold text-slate-800 mt-1 block">{aset.merk || '-'}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">Model / Type</span>
                                <span className="font-semibold text-slate-800 mt-1 block">{aset.model || '-'}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">Serial Number (SN)</span>
                                <span className="font-mono font-semibold text-slate-800 mt-1 block">{aset.serial_number || '-'}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">No. Sertifikat</span>
                                <span className="font-semibold text-slate-800 mt-1 block">{aset.no_sertifikat || '-'}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">Lokasi Ruangan</span>
                                <span className="font-semibold text-slate-800 mt-1 block">{aset.ruangan}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">Lokasi Fisik Deskriptif</span>
                                <span className="font-semibold text-slate-800 mt-1 block">{aset.lokasi_fisik || '-'}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">Tanggal Pengadaan</span>
                                <span className="font-semibold text-slate-800 mt-1 block">{aset.tgl_pengadaan || '-'}</span>
                            </div>
                            <div>
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">Tanggal Kalibrasi</span>
                                <span className="font-semibold text-slate-800 mt-1 block">{aset.tgl_kalibrasi_terakhir || '-'}</span>
                            </div>
                        </div>

                        {aset.keterangan && (
                            <div className="border-t border-slate-100 pt-4">
                                <span className="text-slate-400 block text-xs uppercase tracking-wider font-bold">Catatan Keterangan</span>
                                <p className="text-slate-650 text-sm mt-1 leading-relaxed">{aset.keterangan}</p>
                            </div>
                        )}

                    </div>

                    {/* Right column: Image & QR Code */}
                    <div className="space-y-6 flex flex-col">
                        {/* Gambar Aset Card */}
                        <div className="bg-white border border-slate-100 shadow-sm rounded-2xl p-6 flex flex-col items-center justify-center">
                            <h4 className="font-bold text-slate-800 border-b border-slate-150 w-full text-center pb-2 uppercase tracking-wide text-xs mb-4">Foto Alat Medis</h4>
                            {aset.gambar_aset ? (
                                <img 
                                    src={aset.gambar_aset} 
                                    alt={aset.nama_alat} 
                                    className="w-full h-48 object-cover rounded-xl border border-slate-200 shadow-sm"
                                />
                            ) : (
                                <div className="w-full h-48 bg-slate-50 flex flex-col items-center justify-center rounded-xl border border-slate-100 text-slate-400 font-bold text-lg">
                                    <span className="text-4xl mb-2">🏥</span>
                                    <span>Tidak ada foto</span>
                                </div>
                            )}
                        </div>

                        {/* QR Code Sticker Card */}
                        <div className="bg-white border border-slate-100 shadow-sm rounded-2xl p-6 flex flex-col items-center justify-center space-y-5">
                            <h4 className="font-bold text-slate-800 border-b border-slate-150 w-full text-center pb-2 uppercase tracking-wide text-xs">QR Code Label</h4>
                            {aset.file_qr_code ? (
                                <div className="flex flex-col items-center space-y-4">
                                    <img 
                                        src={aset.file_qr_code} 
                                        alt="QR Code" 
                                        className="w-40 h-40 border border-slate-200 p-2 rounded-xl bg-white shadow-sm"
                                    />
                                    <a
                                        href={aset.file_qr_code}
                                        download
                                        className="px-4 py-2 bg-white border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-semibold rounded-xl transition shadow-sm"
                                    >
                                        Download Sticker QR
                                    </a>
                                </div>
                            ) : (
                                <p className="text-sm text-slate-450">QR Code belum digenerate.</p>
                            )}
                        </div>
                    </div>

                </div>

                {/* Logs History Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    
                    {/* History Log Tracking */}
                    <div className="bg-white border border-slate-100 shadow-sm rounded-2xl p-6 space-y-4">
                        <h4 className="font-semibold text-lg text-slate-800 border-b border-slate-100 pb-2">Riwayat Log Mutasi & Scan</h4>
                        <div className="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                            {trackings.length > 0 ? (
                                trackings.map((t) => (
                                    <div key={t.id_track} className="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                                        <div className="flex justify-between items-center text-xs font-bold text-slate-400">
                                            <span>Ruang: {t.ruangan}</span>
                                            <span>{t.tgl_update}</span>
                                        </div>
                                        <p className="text-sm text-slate-800 font-bold">{t.keterangan}</p>
                                        <p className="text-xs text-slate-500">Pencatat: {t.user}</p>
                                    </div>
                                ))
                            ) : (
                                <p className="text-sm text-slate-400 py-6 text-center">Belum ada riwayat log mutasi.</p>
                            )}
                        </div>
                    </div>

                    {/* History Log Maintenance */}
                    <div className="bg-white border border-slate-100 shadow-sm rounded-2xl p-6 space-y-4">
                        <h4 className="font-semibold text-lg text-slate-800 border-b border-slate-100 pb-2">Riwayat Log Maintenance</h4>
                        <div className="space-y-3 max-h-[300px] overflow-y-auto pr-1">
                            {maintenances.length > 0 ? (
                                maintenances.map((m) => (
                                    <div key={m.id_main} className="p-3 bg-slate-50 rounded-xl border border-slate-100 space-y-1">
                                        <div className="flex justify-between items-center text-xs font-bold text-slate-400">
                                            <span>Tindakan: {m.jenis_tindakan.replace('_', ' ')}</span>
                                            <span>{m.tgl_mulai}</span>
                                        </div>
                                        <p className="text-sm text-slate-800 font-bold">{m.deskripsi_kendala}</p>
                                        <div className="flex justify-between items-center text-xs mt-1">
                                            <span className="text-slate-500">Teknisi: {m.teknisi}</span>
                                            <span className={`px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider ${
                                                m.status_perbaikan === 'Selesai' ? 'bg-emerald-50 text-emerald-700' : 'bg-sky-50 text-sky-700'
                                            }`}>
                                                {m.status_perbaikan}
                                            </span>
                                        </div>
                                    </div>
                                ))
                            ) : (
                                <p className="text-sm text-slate-400 py-6 text-center">Belum ada riwayat pemeliharaan rutin.</p>
                            )}
                        </div>
                    </div>

                </div>

            </div>
        </AuthenticatedLayout>
    );
}
