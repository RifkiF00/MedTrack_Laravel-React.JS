<?php $flash = $data['flash'] ?? null; ?>

<div class="card">

    <!-- HEADER -->
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; gap:15px; flex-wrap:wrap;">
        <div>
            <h3 style="margin:0;">Daftar Aset</h3>
            <p style="margin:8px 0 0; color:#666;">Data alat medis dan sarana prasarana.</p>
        </div>

        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <a href="<?= BASEURL; ?>/aset/scan"
               style="padding:10px 16px; background:#0dcaf0; color:#000; text-decoration:none; border-radius:8px;">
                Scan QR
            </a>

            <a href="<?= BASEURL; ?>/aset/create"
               style="padding:10px 16px; background:#0d6efd; color:#fff; text-decoration:none; border-radius:8px;">
                + Tambah Aset
            </a>
        </div>
    </div>

    <!-- FLASH -->
    <?php if ($flash): ?>
        <div style="margin-bottom:20px; padding:14px; border-radius:8px; background:#eaf7ee; color:#1f7a3d;">
            <?= escape($flash['message']); ?>
        </div>
    <?php endif; ?>

    <!-- FILTER -->
    <form method="GET" action="<?= BASEURL; ?>/aset"
          style="margin-bottom:20px; display:flex; gap:10px; flex-wrap:wrap;">

        <input type="text" name="search"
               placeholder="Cari nama alat / kode..."
               value="<?= escape($data['filter']['search'] ?? '') ?>"
               style="padding:10px; border:1px solid #ccc; border-radius:8px; min-width:220px;">

        <select name="kategori" style="padding:10px; border-radius:8px;">
            <option value="">Semua Kategori</option>
            <option value="Medis" <?= ($data['filter']['kategori'] ?? '')=='Medis' ? 'selected' : '' ?>>Medis</option>
            <option value="Sarpras" <?= ($data['filter']['kategori'] ?? '')=='Sarpras' ? 'selected' : '' ?>>Sarpras</option>
            <option value="IT" <?= ($data['filter']['kategori'] ?? '')=='IT' ? 'selected' : '' ?>>IT</option>
        </select>

        <select name="kondisi" style="padding:10px; border-radius:8px;">
            <option value="">Semua Kondisi</option>
            <option value="Baik" <?= ($data['filter']['kondisi'] ?? '')=='Baik' ? 'selected' : '' ?>>Baik</option>
            <option value="Rusak_Ringan" <?= ($data['filter']['kondisi'] ?? '')=='Rusak_Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
            <option value="Rusak_Berat" <?= ($data['filter']['kondisi'] ?? '')=='Rusak_Berat' ? 'selected' : '' ?>>Rusak Berat</option>
            <option value="Maintenance" <?= ($data['filter']['kondisi'] ?? '')=='Maintenance' ? 'selected' : '' ?>>Maintenance</option>
            <option value="Gudang" <?= ($data['filter']['kondisi'] ?? '')=='Gudang' ? 'selected' : '' ?>>Gudang</option>
        </select>

        <button type="submit"
                style="padding:10px 16px; background:#0d6efd; color:#fff; border:none; border-radius:8px;">
            Filter
        </button>

        <a href="<?= BASEURL; ?>/aset"
           style="padding:10px 16px; background:#6c757d; color:#fff; text-decoration:none; border-radius:8px;">
            Reset
        </a>
    </form>

    <!-- TABLE -->
    <div style="overflow-x:auto;">
        <table style="width:100%; border-collapse:collapse; min-width:950px;">

            <thead style="background:#f8f9fa;">
                <tr>
                    <th style="padding:12px;">No</th>
                    <th style="padding:12px;">Kode</th>
                    <th style="padding:12px;">Nama</th>
                    <th style="padding:12px;">Kategori</th>
                    <th style="padding:12px;">Ruangan</th>
                    <th style="padding:12px;">Kondisi</th>
                    <th style="padding:12px;">Gambar</th>
                    <th style="padding:12px;">Aksi</th>
                </tr>
            </thead>

            <tbody>
                <?php if (!empty($data['aset'])): ?>
                    <?php $no = 1; ?>
                    <?php foreach ($data['aset'] as $aset): ?>
                        <tr style="border-bottom:1px solid #eee;">

                            <td style="padding:12px;"><?= $no++; ?></td>

                            <td style="padding:12px;"><?= escape($aset->kode_label ?? '-'); ?></td>

                            <td style="padding:12px;"><?= escape($aset->nama_alat ?? '-'); ?></td>

                            <td style="padding:12px;"><?= escape($aset->kategori_aset ?? '-'); ?></td>

                            <td style="padding:12px;"><?= escape($aset->nama_ruang ?? '-'); ?></td>

                            <td style="padding:12px;">
                                <?php
                                $kondisi = $aset->status_kondisi ?? '';
                                $kondisiDisplay = str_replace('_', ' ', $kondisi);
                                $color = '#f3f4f6';

                                if ($kondisi == 'Baik') $color = '#d1fae5';
                                elseif ($kondisi == 'Rusak_Ringan') $color = '#fef3c7';
                                elseif ($kondisi == 'Rusak_Berat') $color = '#fee2e2';
                                elseif ($kondisi == 'Maintenance') $color = '#e0f2fe';
                                elseif ($kondisi == 'Gudang') $color = '#f1f5f9';
                                else $color = '#f3f4f6';
                                ?>

                                <span style="padding:5px 10px; border-radius:999px; background:<?= $color ?>;">
                                    <?= escape($kondisiDisplay); ?>
                                </span>
                            </td>

                            <!-- GAMBAR -->
                            <td style="padding:12px;">
                                <?php if (!empty($aset->gambar_aset)): ?>
                                    <img src="<?= BASEURL; ?>/uploads/assets/<?= escape($aset->gambar_aset); ?>"
                                         width="45"
                                         style="border-radius:6px;">
                                <?php else: ?>
                                    <span style="color:#999;">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- AKSI -->
                            <td style="padding:12px;">
                                <div style="display:flex; gap:6px; flex-wrap:wrap;">

                                    <a href="<?= BASEURL; ?>/aset/detail/<?= $aset->id_aset; ?>"
                                       style="background:#198754; color:#fff; padding:6px 10px; border-radius:6px; text-decoration:none;">
                                        Detail
                                    </a>

                                    <a href="<?= BASEURL; ?>/aset/edit/<?= $aset->id_aset; ?>"
                                       style="background:#ffc107; color:#222; padding:6px 10px; border-radius:6px; text-decoration:none;">
                                        Edit
                                    </a>

                                    <form action="<?= BASEURL; ?>/aset/delete/<?= $aset->id_aset; ?>"
                                          method="POST"
                                          onsubmit="return confirm('Hapus aset ini?');"
                                          style="margin:0;">

                                        <input type="hidden" name="csrf_token" value="<?= getCSRFToken(); ?>">

                                        <button type="submit"
                                                style="background:#dc3545; color:#fff; padding:6px 10px; border:none; border-radius:6px; cursor:pointer;">
                                            Hapus
                                        </button>
                                    </form>

                                </div>
                            </td>

                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" style="text-align:center; padding:20px; color:#666;">
                            Belum ada data
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>

        </table>
    </div>

</div>