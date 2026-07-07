# Flood Segmentation — Landing Page

Landing page riset untuk paper **"Segmentasi Semantik Banjir Melalui Integrasi
Backbone ResNet-50 pada Citra Optik RGB"** (JUTIM, Universitas Bina Insan
Lubuklinggau). Dibangun dengan **Laravel** (presentasi & data) dan dilengkapi
satu layanan kecil **Python/Flask** untuk widget simulasi metrik.

> ⚠️ Layanan Python di sini **tidak menjalankan model U-Net++/DeepLabV3 yang
> sesungguhnya** — tidak ada bobot model terlatih yang disertakan. Endpoint
> simulasi hanya mengembalikan angka acak namun realistis di sekitar rentang
> hasil yang dilaporkan pada paper, sekadar untuk mendemonstrasikan pola
> arsitektur "Laravel di depan, layanan Python terpisah di belakang".

## Struktur proyek

```
flood-landing/
├── app/Http/Controllers/LandingController.php   # data konten halaman
├── resources/views/landing.blade.php             # markup halaman
├── public/css/landing.css                        # desain (token warna/tipografi)
├── public/js/landing.js                           # interaksi (slider, scroll-reveal, fetch demo)
├── routes/web.php                                 # route "/" -> LandingController
└── python-service/
    ├── app.py             # Flask API: GET /api/simulate
    └── requirements.txt
```

Sisanya adalah skeleton standar Laravel (boleh diabaikan untuk halaman ini —
tidak ada database/migration yang dipakai oleh landing page).

## Menjalankan bagian Laravel

Butuh PHP 8.3+ dan Composer terpasang di komputer kamu (composer perlu akses
internet ke packagist.org, yang tidak tersedia di sandbox tempat kode ini
dibuat — jadi langkah `composer install` di bawah perlu dijalankan di
komputermu sendiri).

```bash
cd flood-landing
composer install
cp .env.example .env
php artisan key:generate
php artisan serve
```

Buka **http://localhost:8000** di browser.

## Menjalankan layanan simulasi Python (opsional)

Diperlukan hanya untuk tombol "Coba Simulasi" di halaman. Tanpa ini, sisa
halaman tetap berfungsi normal — tombol hanya akan menampilkan pesan bahwa
layanan belum aktif.

```bash
cd flood-landing/python-service
pip install -r requirements.txt
python3 app.py
```

Layanan berjalan di **http://localhost:5000**. Sudah diuji dan mengembalikan
JSON seperti:

```json
{"model": "DeepLabV3", "iou": 94.27, "recall": 95.54, "dice": 97.03, "note": "..."}
```

Jika kamu menjalankan layanan Python di alamat/port lain, ubah konstanta
`SIM_API` di `public/js/landing.js`.

## Mengubah konten

Semua teks dan angka (judul, statistik hero, tabel metrik, profil
confusion-matrix, rekomendasi, saran pengembangan) didefinisikan sebagai
array PHP di `app/Http/Controllers/LandingController.php` — ubah di satu
tempat itu, tidak perlu menyentuh file Blade.

## Catatan desain

Tema visual "peta satelit malam hari" dengan dua warna aksen:
- **Cyan (`#3fc6d9`)** — mewakili DeepLabV3 / warna overlay masker air.
- **Coral (`#ff7a59`)** — mewakili U-Net++ / sensitivitas-recall.

Elemen interaktif utama adalah slider pembanding pada bagian Arsitektur —
ilustrasi konseptual (bukan output model asli) yang menunjukkan trade-off
antara presisi tepi (U-Net++) dan cakupan luas (DeepLabV3).
