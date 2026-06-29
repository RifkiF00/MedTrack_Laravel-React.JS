# Profile Images Directory

## Penempatkan Gambar Profil Pengguna

Letakkan file gambar profil pengguna di direktori ini dengan nama file:
```
{id_user}.png
```

### Contoh:
- User ID 1: `1.png`
- User ID 2: `2.png`
- User ID 3: `3.png`

### Format yang Didukung:
- PNG (Rekomendasi)
- JPG
- JPEG

### Ukuran Maksimal:
- 5MB per file
- Resolusi rekomendasi: 256x256px atau lebih tinggi

### Struktur Folder:
```
public/
├── uploads/
│   ├── profiles/
│   │   ├── 1.png
│   │   ├── 2.png
│   │   └── 3.png
│   ├── assets/
│   ├── troubleshoot/
│   └── ...
```

Jika gambar profil tidak ditemukan, sistem akan menampilkan gambar default avatar.
