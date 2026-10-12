# 🤖 AGENTS.md — AI Agent Guidelines & Repository Handbook
## MagNet — Sistem Informasi Manajemen & Pendaftaran Magang
**Badan Pusat Statistik (BPS) Kabupaten Bantul**

---

## 1. 📌 Ikhtisar Proyek & Repositori

Selamat datang di repositori **MagNet (*Magang Network*)**. Repositori ini berisi sistem informasi manajemen dan pendaftaran magang/PKL terpadu untuk **Badan Pusat Statistik (BPS) Kabupaten Bantul, Daerah Istimewa Yogyakarta**.

* **Teknologi Utama:** Laravel 11.x, PHP 8.2+, MySQL 8.0+, Tailwind CSS, Alpine.js, Chart.js.
* **Integrasi Eksternal:** Google OAuth 2.0 (Socialite), Google Gemini 1.5 Flash API (MagBot), SMTP Mailer.
* **Pola Desain Arsitektur:** Modular Monolithic dengan *Service Layer Pattern* (*Thin Controller, Fat Service*).
* **Dokumentasi Terkait:**
  * [PRD Document](file:///c:/xampp/htdocs/magnet/docs/prd.md) — Kebutuhan fungsional & non-fungsional.
  * [Architecture Document](file:///c:/xampp/htdocs/magnet/docs/architecture.md) — Arsitektur teknis 5-tier & ADR.
  * [System Design Document](file:///c:/xampp/htdocs/magnet/docs/system_design.md) — Perancangan detail, UML, kamus data & kontrak rute.

---

## 2. 🗺️ Struktur Direktori Kunci

```
magnet/
├── app/
│   ├── Http/
│   │   ├── Controllers/       -> Hanya menangani request/response (Thin Controllers)
│   │   ├── Middleware/        -> AdminMiddleware ('admin'), Auth, Guest
│   │   └── Requests/          -> Form Request Validation (Lowongan, Magang, Pendaftaran)
│   ├── Models/                -> Eloquent ORM (User, Lowongan, Pendaftaran, Magang, Setting)
│   ├── Services/              -> SELURUH logika bisnis & transaksi (Fat Services)
│   │   ├── ChatbotService.php           -> Integrasi Google Gemini API & prompt dinamis
│   │   ├── DashboardAnalyticsService.php-> Agregasi statistik, tren & grafik
│   │   ├── FileUploadService.php        -> Upload berkas, sanitasi path & delete
│   │   ├── MagangService.php            -> Manajemen direktori peserta & visibilitas
│   │   └── PendaftaranService.php       -> Alur seleksi, approval & notifikasi email
│   └── Mail/                  -> Mailable (PendaftaranStatusMail)
├── database/
│   └── migrations/            -> Skema tabel database (users, lowongans, pendaftarans, magangs, settings)
├── docs/                      -> Dokumentasi lengkap (prd.md, architecture.md, system_design.md, agents.md)
├── resources/
│   └── views/                 -> Blade Templates (layouts, components, daftar, lowongan, magang, divisi, settings)
└── routes/
    ├── web.php                -> Rute aplikasi utama & otorisasi role
    └── auth.php               -> Rute autentikasi lokal & Google OAuth SSO
```

---

## 3. ⚖️ Aturan & Konvensi Pengkodean (*Coding Standards*)

Ketika bekerja pada basis kode ini, setiap agen AI **WAJIB** mematuhi pedoman berikut:

### 3.1. Pola Service Layer (Dilarang Meletakkan Logika Berat di Controller)
* **Controller:** Hanya bertugas memvalidasi *request*, memanggil *Service Layer*, dan mengembalikan *View*, *Redirect*, atau *JsonResponse*.
* **Service:** Seluruh manipulasi data, kalkulasi matematika/waktu, unggah file, dan pengiriman email **harus berada di dalam `app/Services/`**.

### 3.2. Integritas Transaksi Database (ACID)
* Setiap mutasi data yang melibatkan lebih dari satu tabel atau langkah kritikal (misal: *Approval pendaftaran yang memindahkan data ke tabel `magangs` dan mengubah status pendaftaran*) **WAJIB dibungkus dalam blok `DB::transaction()`**.

### 3.3. Penanganan Siklus Hidup Peserta (`Magang::boot()`)
* Durasi magang (`periode_bulan`) dan status kepesertaan (`belum_aktif`, `aktif`, `selesai`) dikalkulasi secara terpusat oleh *model observer hook* di [`Magang.php`](file:///c:/xampp/htdocs/magnet/app/Models/Magang.php).
* **Jangan melakukan kalkulasi manual `periode_bulan` di tempat lain** agar tidak terjadi inkonsistensi data.

### 3.4. Validasi Masukan & Proteksi File
* Gunakan `FormRequest` untuk validasi formulir utama.
* Berkas PDF (`surat_permohonan`, `surat_kampus`) wajib dibatasi maksimal **1024 KB** dengan validasi MIME `mimes:pdf`.
* Pas foto wajib bertipe gambar (`jpeg, png, jpg`) dengan batasan maksimal **2048 KB**.
* Gunakan [`FileUploadService`](file:///c:/xampp/htdocs/magnet/app/Services/FileUploadService.php) untuk operasi file; jangan gunakan `file_put_contents` atau manipulasi filesystem mentah.

### 3.5. Keamanan & RBAC (Role-Based Access Control)
* Selalu periksa hak akses:
  * Rute admin dilindungi middleware `['auth', 'admin']`.
  * Rute user yang mengakses resource pribadi wajib memverifikasi kepemilikan: `$model->user_id === auth()->id()`.
  * Akun `admin` **dilarang** menggunakan Google OAuth SSO (wajib kredensial internal email/password).

---

## 4. 🛠️ Perintah & Alur Kerja Lingkungan Kerja (*Workflows*)

### 4.1. Menjalankan Server Lokal
```bash
# Jalankan web server Laravel (default port 8000)
php artisan serve

# Kompilasi aset Vite (Tailwind CSS & JS)
npm run dev
```

### 4.2. Database & Storage
```bash
# Menjalankan migrasi database
php artisan migrate

# Menghubungkan direktori storage ke public (WAJIB untuk pas foto dan berkas)
php artisan storage:link

# Membersihkan cache aplikasi saat ada perubahan config/route
php artisan optimize:clear
```

---

## 5. 🤖 Konteks Bisnis Domain BPS Kabupaten Bantul

1. **Struktur Divisi:** BPS Bantul memiliki divisi kerja spesifik:
   * Seksi IPDS (Integrasi Pengolahan & Diseminasi Statistik)
   * Seksi Statistik Sosial
   * Seksi Statistik Distribusi
   * Seksi Statistik Produksi
   * Seksi Neraca Wilayah & Analisis Statistik (Nerwilis)
   * Subbagian Umum
2. **Durasi Magang:** Durasi minimal magang bersifat dinamis (default: 3 bulan) dan dikontrol melalui tabel `settings` (`min_durasi_magang`).
3. **Keputusan Seleksi:** Hanya terdiri dari 4 status resmi:
   * `pending` — Menunggu verifikasi berkas oleh admin.
   * `approved` — Disetujui, pendaftar resmi menjadi peserta magang.
   * `conditional` — Setuju bersyarat, pendaftar wajib memperbaiki dokumen sesuai `remarks`.
   * `rejected` — Permohonan ditolak dengan alasan tercatat pada `remarks`.
4. **MagBot AI Engine:**
   * Model: Google Gemini 1.5 Flash via REST API.
   * Prompt disusun secara dinamis oleh [`ChatbotService`](file:///c:/xampp/htdocs/magnet/app/Services/ChatbotService.php) dengan menyuntikkan data divisi dan lowongan terkini langsung dari basis data.

---

## 6. 🚫 Larangan Keras (*Strict Restrictions*)

1. ❌ **Jangan menghapus komentar (*comments*) atau *docstrings* yang sudah ada** kecuali diperintahkan secara spesifik.
2. ❌ **Jangan mengekspos API Key atau rahasia sensitif** (Gemini API Key, Google OAuth Client Secret, Mail Password) ke dalam repositori. Selalu gunakan helper `env()` atau `config()`.
3. ❌ **Jangan gunakan sintaks Blade `{!! !!}` untuk masukan pengguna** guna mencegah kerentanan XSS. Selalu gunakan `{{ }}`.
4. ❌ **Jangan mematikan CSRF Protection** pada rute stateful (`web.php`).
5. ❌ **Jangan mengubah loose comparison `!=` untuk ID user**; selalu gunakan strict comparison `!==` untuk mencegah celah keamanan tipe data PHP.

---

## 7. 🎯 Definisi Selesai (*Definition of Done - DoD*)

Sebelum menyelesaikan tugas atau *pull request* yang melibatkan modifikasi kode:
- [ ] Logika bisnis ditempatkan pada kelas Service terkait (bukan di Controller atau Blade).
- [ ] Tidak ada regresi pada alur transaksi kritis (pendaftaran, verifikasi berkas, kirim email).
- [ ] Formulir divalidasi dengan aman di sisi *backend* dan ramah pengguna di sisi *frontend*.
- [ ] Tidak ada sintaks error atau pengecualian tidak tertangani di `storage/logs/laravel.log`.
