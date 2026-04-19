<div class="card" style="padding: 24px; border-radius: 16px; background: #ffffff;">
    <!-- HEADER -->
    <div style="margin-bottom: 24px;">
        <h1 style="margin: 0 0 8px 0; font-size: 28px; font-weight: 700; color: #1a2b56; font-family: 'Nunito', sans-serif;">
            Dokumen Mutu
        </h1>
        <p style="margin: 0; font-size: 15px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
            Arsip dokumen kalibrasi, sertifikat, dan laporan pemeliharaan aset
        </p>
    </div>

    <!-- FLASH MESSAGE -->
    <?php if (!empty($data['flash'])): ?>
        <div style="margin-bottom: 20px; padding: 12px 16px; border-radius: 12px; background: #ecfdf5; color: #047857; font-size: 14px; font-family: 'Nunito', sans-serif;">
            ✓ <?= escape($data['flash']['message']); ?>
        </div>
    <?php endif; ?>

    <!-- FILTER & SEARCH -->
    <div style="margin-bottom: 24px; display: flex; gap: 12px; flex-wrap: wrap;">
        <input type="text" placeholder="Cari dokumen atau aset..."
               style="flex: 1; min-width: 200px; padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff;"
               onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'"
               onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
        <select style="padding: 11px 12px; border: 1px solid #e5e7eb; border-radius: 10px; font-size: 14px; font-family: 'Nunito', sans-serif; color: #1a2b56; background: #ffffff;"
                onfocus="this.style.borderColor='#3d6aff'; this.style.boxShadow='0 0 0 3px rgba(61, 106, 255, 0.1)'"
                onblur="this.style.borderColor='#e5e7eb'; this.style.boxShadow='none'">
            <option value="">-- Semua Kategori --</option>
            <option value="kalibrasi">Kalibrasi</option>
            <option value="sertifikat">Sertifikat</option>
            <option value="laporan">Laporan Pemeliharaan</option>
        </select>
        <button style="padding: 11px 18px; background: #3d6aff; color: #ffffff; border: none; border-radius: 10px; font-size: 14px; font-weight: 600; font-family: 'Nunito', sans-serif; cursor: pointer; transition: all 0.2s;"
                onmouseover="this.style.background='#2952cc'"
                onmouseout="this.style.background='#3d6aff'">
            + Upload Dokumen
        </button>
    </div>

    <!-- DOCUMENT LIST -->
    <?php if (!empty($data['dokumen_list'])): ?>
        <div style="display: grid; gap: 12px;">
            <?php foreach ($data['dokumen_list'] as $dokumen): ?>
                <div style="padding: 16px; border: 1px solid #e5e7eb; border-radius: 12px; background: #ffffff;">
                    <div style="display: flex; justify-content: space-between; align-items: start; gap: 12px;">
                        <div style="display: flex; gap: 12px; flex: 1;">
                            <div style="width: 48px; height: 48px; background: linear-gradient(135deg, #3d6aff, #2952cc); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: #ffffff; font-size: 24px;">
                                📄
                            </div>
                            <div style="flex: 1;">
                                <h3 style="margin: 0 0 4px 0; font-size: 16px; font-weight: 600; color: #1a2b56; font-family: 'Nunito', sans-serif;">
                                    <?= escape($dokumen->nama_dokumen); ?>
                                </h3>
                                <p style="margin: 0 0 6px 0; font-size: 13px; color: #8e9bb0; font-family: 'Nunito', sans-serif;">
                                    <?= escape($dokumen->kode_label ?? 'N/A'); ?> - <?= escape($dokumen->nama_alat ?? 'Umum'); ?>
                                </p>
                                <p style="margin: 0; font-size: 12px; color: #6b7280; font-family: 'Nunito', sans-serif;">
                                    📅 <?= date('d/m/Y', strtotime($dokumen->tgl_upload)); ?> • 📝 <?= escape($dokumen->kategori); ?>
                                </p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 6px; flex-wrap: wrap; justify-content: flex-end;">
                            <span style="display: inline-block; padding: 4px 10px; background: #eff6ff; color: #0369a1; border-radius: 8px; font-size: 11px; font-weight: 500; font-family: 'Nunito', sans-serif;">
                                <?php
                                $katLabel = [
                                    'kalibrasi' => '🔧 Kalibrasi',
                                    'sertifikat' => '✓ Sertifikat',
                                    'laporan' => '📊 Laporan'
                                ];
                                echo $katLabel[$dokumen->kategori] ?? $dokumen->kategori;
                                ?>
                            </span>
                            <a href="<?= BASEURL; ?>/dokumen/download/<?= $dokumen->id_dokumen; ?>" style="padding: 6px 12px; background: #f0f4f8; color: #0369a1; text-decoration: none; border-radius: 8px; font-size: 12px; font-weight: 600; font-family: 'Nunito', sans-serif;">
                                ⬇ Download
                            </a>
                            <button style="padding: 6px 12px; background: #fef2f2; color: #dc2626; border: none; border-radius: 8px; font-size: 12px; font-weight: 600; font-family: 'Nunito', sans-serif; cursor: pointer;"
                                    onclick="if(confirm('Hapus dokumen ini?')) { window.location='<?= BASEURL; ?>/dokumen/delete/<?= $dokumen->id_dokumen; ?>'; }">
                                🗑 Hapus
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div style="padding: 48px 16px; text-align: center; color: #8e9bb0; border-radius: 12px; background: #f9fafb;">
            <i class="bi bi-file-earmark-text" style="font-size: 48px; display: block; margin-bottom: 16px;"></i>
            <div style="font-size: 16px; font-weight: 500; margin-bottom: 8px; font-family: 'Nunito', sans-serif;">Belum ada dokumen mutu</div>
            <div style="font-size: 14px; font-family: 'Nunito', sans-serif;">Upload dokumen kalibrasi, sertifikat, dan laporan aset</div>
        </div>
    <?php endif; ?>
</div>
