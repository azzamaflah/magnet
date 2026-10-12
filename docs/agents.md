# 🤖 Panduan Agen AI & Pedoman Kontributor (AGENTS.md)
## MagNet — Sistem Informasi Manajemen & Pendaftaran Magang
**Badan Pusat Statistik (BPS) Kabupaten Bantul**

> Dokumen ini merupakan salinan dokumentasi resmi dari file pedoman inti [`AGENTS.md`](file:///c:/xampp/htdocs/magnet/AGENTS.md) yang berada pada direktori *root* repositori. Dokumen ini ditujukan sebagai panduan bagi Agen AI (seperti Antigravity, Claude, Gemini, GitHub Copilot) serta pengembang perangkat lunak yang berkontribusi pada pengembangan sistem MagNet.

---

## 1. 📌 Ikhtisar Repositori
* **Nama Sistem:** MagNet (*Magang Network*)
* **Instansi:** BPS Kabupaten Bantul, D.I. Yogyakarta
* **Stack:** Laravel 11.x, PHP 8.2+, MySQL 8.0+, Tailwind CSS, Alpine.js, Chart.js
* **Dokumen Terkait:**
  * [Product Requirements Document (PRD)](file:///c:/xampp/htdocs/magnet/docs/prd.md)
  * [Software Architecture Document (Architecture)](file:///c:/xampp/htdocs/magnet/docs/architecture.md)
  * [System Design Document (SDD)](file:///c:/xampp/htdocs/magnet/docs/system_design.md)

---

## 2. 🗺️ Struktur Direktori Kunci
* `app/Http/Controllers/` : *Thin Controllers* (hanya request handling & response).
* `app/Services/` : *Fat Services* (seluruh logika bisnis, transaksi DB, kalkulasi, upload file).
  * `ChatbotService.php` : Integrasi Google Gemini API & dynamic prompt injection.
  * `DashboardAnalyticsService.php` : Kalkulasi statistik agregat, tren pendaftaran & sebaran kampus.
  * `FileUploadService.php` : Penanganan berkas digital, hashing nama file, dan penghapusan.
  * `MagangService.php` : Pengelolaan data peserta aktif, alumni, dan visibilitas direktori.
  * `PendaftaranService.php` : Alur seleksi, transaksi DB persetujuan, dan pengiriman email status.
* `app/Models/` : Eloquent ORM (`User`, `Lowongan`, `Pendaftaran`, `Magang`, `Setting`).
* `resources/views/` : Blade Templates dengan antarmuka Tailwind CSS & interaktivitas Alpine.js.
* `docs/` : Dokumentasi teknis sistem (`prd.md`, `architecture.md`, `system_design.md`, `agents.md`).

---

## 3. ⚖️ Aturan Desain & Pengkodean Wajib

1. **Service Layer Pattern:** Seluruh logika bisnis wajib berada di `app/Services/`. Dilarang menuliskan query kompleks atau transaksi berat di Controller maupun Blade.
2. **Integritas Transaksi ACID:** Mutasi data bertahap wajib dibungkus `DB::transaction()`.
3. **Model Boot Observer:** Durasi (`periode_bulan`) dan status siklus peserta (`belum_aktif`, `aktif`, `selesai`) dikalkulasi secara terpusat di `Magang::boot()`. Dilarang mengkalkulasi manual di luar model.
4. **Validasi Berkas:** PDF maksimal 1024 KB (`mimes:pdf`), Pas Foto maksimal 2048 KB (`jpeg, png, jpg`). Gunakan `FileUploadService`.
5. **Keamanan & Otorisasi:** Rute admin dilindungi middleware `['auth', 'admin']`. Rute user memverifikasi kepemilikan (`user_id === auth()->id()`). Admin dilarang menggunakan Google SSO.

---

## 4. 🛠️ Perintah Kerja Harian
```bash
# Menjalankan Server Lokal
php artisan serve
npm run dev

# Manajemen Database & Storage
php artisan migrate
php artisan storage:link
php artisan optimize:clear
```

---

## 5. 🤖 Konteks Bisnis BPS Bantul
* **Divisi Resmi:** Seksi IPDS, Statistik Sosial, Statistik Distribusi, Statistik Produksi, Nerwilis, dan Subbagian Umum.
* **Durasi Magang:** Default minimal 3 bulan (dapat diubah dinamis oleh admin melalui tabel `settings`).
* **Status Seleksi:** `pending` (menunggu), `approved` (disetujui), `conditional` (revisi berkas), dan `rejected` (ditolak).
* **AI MagBot:** Engine Google Gemini 1.5 Flash via REST API dengan injeksi data divisi dan lowongan real-time dari database.

---

## 6. 🚫 Batasan Operasional
* Dilarang menghapus komentar kode dan docstring yang sudah ada.
* Dilarang mengekspos kredensial/API Key ke kode (selalu gunakan `.env`).
* Dilarang menggunakan sintaks unescaped `{!! !!}` untuk data input pengguna.
* Dilarang mematikan proteksi CSRF.
* Gunakan strict comparison `!==` untuk perbandingan ID pengguna.
