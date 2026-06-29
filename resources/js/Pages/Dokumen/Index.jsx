import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head } from '@inertiajs/react';

export default function Index({ auth, role }) {
    const reports = [
        {
            id: 'total_aset',
            title: 'Laporan Total Aset',
            desc: 'Daftar semua inventaris alat medis, sarana, dan IT lengkap dengan ruangan dan kondisi kelayakan.',
            icon: '📋',
            url: route('dokumen.export', 'total_aset')
        },
        {
            id: 'ruangan_aset',
            title: 'Laporan Ruangan & Aset',
            desc: 'Rekapitulasi total ruangan penempatan beserta kalkulasi jumlah unit aset terdaftar per ruangan.',
            icon: '🏢',
            url: route('dokumen.export', 'ruangan_aset')
        },
        {
            id: 'sdm_staff',
            title: 'Laporan SDM & Staf',
            desc: 'Daftar data personel dan staf operasional rumah sakit berserta role hak akses sistem.',
            icon: '👥',
            url: route('dokumen.export', 'sdm_staff')
        },
        {
            id: 'maintenance',
            title: 'Laporan Maintenance Bulanan',
            desc: 'Daftar riwayat dan rencana pengerjaan preventive maintenance (PM) terjadwal bulan ini.',
            icon: '⚙️',
            url: route('dokumen.export', 'maintenance')
        }
    ];

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div>
                    <h2 className="font-semibold text-2xl text-gray-800 leading-tight">Dokumen Mutu</h2>
                    <p className="text-sm text-gray-500 mt-1">Cetak laporan inventarisasi, ruangan, SDM, dan logs maintenance resmi</p>
                </div>
            }
        >
            <Head title="Dokumen Mutu - MedTrack IPSRS" />

            <div className="py-10">
                <div className="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
                    <div className="bg-white border border-gray-100 rounded-3xl p-6 shadow-sm">
                        <h3 className="font-bold text-lg text-gray-900 border-b border-gray-100 pb-3 mb-6">Pusat Cetak Dokumen & Laporan</h3>
                        
                        <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {reports.map((report) => (
                                <div key={report.id} className="border border-gray-100 rounded-2xl p-5 hover:border-indigo-300 hover:shadow-md transition duration-200 bg-gray-50/30 flex flex-col justify-between space-y-4">
                                    <div className="space-y-2">
                                        <span className="text-3xl block">{report.icon}</span>
                                        <h4 className="font-bold text-base text-gray-900">{report.title}</h4>
                                        <p className="text-xs text-gray-500 leading-relaxed">{report.desc}</p>
                                    </div>
                                    <a
                                        href={report.url}
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        className="w-full text-center py-2.5 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl text-xs font-semibold tracking-wide transition block shadow-sm"
                                    >
                                        🖨️ Cetak / Ekspor PDF
                                    </a>
                                </div>
                            ))}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
