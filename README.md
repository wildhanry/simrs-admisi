# SIMRS Admisi - Sistem Informasi Manajemen Rumah Sakit

Sistem Informasi Manajemen Rumah Sakit (SIMRS) untuk pengelolaan pendaftaran pasien rawat jalan dan rawat inap.

![Laravel](https://img.shields.io/badge/Laravel-12.46.0-FF2D20?style=flat&logo=laravel)
![PHP](https://img.shields.io/badge/PHP-8.3.13-777BB4?style=flat&logo=php)
![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-4.0-38B2AC?style=flat&logo=tailwind-css)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=flat&logo=mysql)

## 📋 Fitur Utama

### Manajemen Pasien
- ✅ Registrasi pasien baru dengan nomor rekam medis otomatis
- ✅ Pencarian pasien (AJAX live search)
- ✅ Data lengkap pasien (NIK, demografi, kontak darurat)
- ✅ Cetak kartu pasien dengan QR Code

### Pendaftaran Rawat Jalan
- ✅ Pendaftaran ke poliklinik
- ✅ Pemilihan dokter berdasarkan poliklinik
- ✅ Sistem antrian otomatis (format: OP-YYYYMMDD-POLYCODE-XXX)
- ✅ Cetak bukti pendaftaran dengan QR Code
- ✅ Metode pembayaran: Tunai, BPJS, Asuransi, Perusahaan

### Pendaftaran Rawat Inap
- ✅ Booking tempat tidur dengan validasi ketersediaan
- ✅ Pessimistic locking untuk mencegah double booking
- ✅ Manajemen ruangan dan bed
- ✅ Cetak surat perawatan dengan QR Code

### Laporan
- ✅ Laporan pendaftaran dengan filter tanggal
- ✅ Filter berdasarkan jenis, status, metode pembayaran
- ✅ Statistik real-time (total pasien, pendaftaran hari ini, dll)
- ✅ Export to PDF (landscape A4)

### Panel Admin
- ✅ Manajemen pengguna (admin/staff)
- ✅ Manajemen dokter dengan spesialisasi
- ✅ Manajemen poliklinik
- ✅ Manajemen ruangan dan tempat tidur
- ✅ Dashboard dengan statistik lengkap

## 🛠️ Teknologi

- **Backend**: Laravel 12.46.0 (PHP 8.3.13)
- **Frontend**: Tailwind CSS 4, Alpine.js 3.x
- **Database**: MySQL 8.0
- **PDF Generator**: barryvdh/laravel-dompdf
- **QR Code**: simplesoftwareio/simple-qrcode 4.2.0
- **Build Tool**: Vite 7.3.1

## 📦 Instalasi

### Persyaratan

**Metode 1: Manual Install**
- PHP >= 8.2
- Composer
- Node.js >= 16
- MySQL >= 8.0

**Metode 2: Docker (Recommended)**
- Docker Engine 20.10+
- Docker Compose 2.0+

---

### 🐳 Instalasi dengan Docker (Recommended)

**Quick Start:**
```bash
# Clone repository
git clone https://github.com/wildhanry/simrs-admisi.git
cd simrs-admisi

# Setup environment
cp .env.docker .env

# Build dan jalankan
docker-compose up -d

# Akses aplikasi
open http://localhost:8000
```

Aplikasi akan berjalan di:
- **SIMRS Admisi**: http://localhost:8000
- **phpMyAdmin**: http://localhost:8080

📖 **Panduan lengkap Docker**: Lihat [DOCKER.md](DOCKER.md)

---

### 💻 Instalasi Manual

1. **Clone repository**
```bash
git clone https://github.com/wildhanry/simrs-admisi.git
cd simrs-admisi
```

2. **Install dependencies**
```bash
composer install
npm install
```

3. **Setup environment**
```bash
cp .env.example .env
php artisan key:generate
```

4. **Konfigurasi database di `.env`**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=simrs_admisi
DB_USERNAME=root
DB_PASSWORD=
```

5. **Jalankan migrasi dan seeder**
```bash
php artisan migrate --seed
```

6. **Build assets**
```bash
npm run build
```

7. **Jalankan server**
```bash
php artisan serve
```

Aplikasi akan berjalan di `http://127.0.0.1:8000`

## 👤 Default User

### Admin
- Email: `admin@simrs.local`
- Password: `password`

### Staff
- Email: `staff@simrs.local`
- Password: `password`

## 📊 Database Schema

### Tabel Utama

#### `patients` - Data Pasien
- medical_record_number (auto-generated)
- nik, name, birth_place, birth_date
- gender, blood_type, phone, address
- emergency_contact_name, emergency_contact_phone

#### `registrations` - Pendaftaran (Rawat Jalan & Rawat Inap)
- registration_number (auto-generated)
- queue_number (untuk rawat jalan)
- patient_id, doctor_id, polyclinic_id
- type (outpatient/inpatient)
- status (waiting/in_progress/completed/cancelled)
- payment_method (cash/bpjs/insurance/company)

#### `doctors` - Data Dokter
- sip_number, name, specialization
- polyclinic_id (relasi ke poliklinik)
- is_active

#### `polyclinics` - Poliklinik
- code, name, description
- is_active

#### `wards` - Ruangan Rawat Inap
- code, name, ward_class, capacity
- is_active

#### `beds` - Tempat Tidur
- ward_id, bed_number
- status (available/occupied/maintenance)

## 🔐 Role & Permission

### Admin
- Akses penuh ke semua fitur
- Manajemen master data (dokter, poliklinik, ruangan, bed)
- Manajemen pengguna

### Staff
- Pendaftaran pasien rawat jalan
- Pendaftaran pasien rawat inap
- Manajemen data pasien
- Akses laporan

## 🎨 Fitur UI/UX

- ✅ Responsive design (mobile, tablet, desktop)
- ✅ Sidebar navigation dengan Alpine.js
- ✅ Dark mode support (topbar)
- ✅ Live search dengan AJAX
- ✅ Form validation real-time
- ✅ Loading states & error handling
- ✅ Toast notifications
- ✅ Professional hospital theme (blue gradient)
- ✅ Bahasa Indonesia full support

## 📝 Workflow Pendaftaran

### Rawat Jalan
1. Staff memilih/mencari pasien
2. Pilih poliklinik
3. Sistem menampilkan dokter sesuai poliklinik
4. Input keluhan dan metode pembayaran
5. Sistem generate nomor antrian otomatis
6. Cetak bukti pendaftaran dengan QR Code

### Rawat Inap
1. Staff memilih/mencari pasien
2. Pilih ruangan
3. Sistem menampilkan bed yang tersedia
4. Pilih bed dan konfirmasi
5. Sistem lock bed (pessimistic locking)
6. Cetak surat perawatan dengan QR Code

## 🔧 Konfigurasi

### Queue Number Format
```php
OP-YYYYMMDD-POLYCODE-XXX
// Contoh: OP-20260113-UMUM-001
```

### Medical Record Number Format
```php
MR-YYYYMMDD-XXXX
// Contoh: MR-20260113-0001
```

### Registration Number Format
```php
REG-YYYYMMDD-XXXX
// Contoh: REG-20260113-0001
```

## 📄 Struktur Proyek

```
simrs-admisi/
├── app/
│   ├── Http/Controllers/
│   │   ├── DashboardController.php
│   │   ├── PatientController.php
│   │   ├── OutpatientRegistrationController.php
│   │   ├── InpatientRegistrationController.php
│   │   ├── ReportController.php
│   │   └── Admin/
│   ├── Models/
│   │   ├── Patient.php
│   │   ├── Registration.php
│   │   ├── Doctor.php
│   │   ├── Polyclinic.php
│   │   ├── Ward.php
│   │   └── Bed.php
│   └── Services/
│       ├── QueueService.php
│       └── BedAllocationService.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   ├── views/
│   │   ├── layouts/
│   │   ├── dashboard.blade.php
│   │   ├── patients/
│   │   ├── outpatient/
│   │   ├── inpatient/
│   │   ├── reports/
│   │   └── admin/
│   ├── css/
│   └── js/
└── routes/
    └── web.php
```

## 🐛 Troubleshooting

### Error: "Sessions table not found"
```bash
php artisan migrate
```

### Error: "Vite manifest not found"
```bash
npm run build
```

### Error: "Column 'queue_number' not found"
```bash
php artisan migrate:fresh --seed
```

## 🤝 Kontribusi

Contributions are welcome! Please follow these steps:

1. Fork repository
2. Create feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to branch (`git push origin feature/AmazingFeature`)
5. Open Pull Request

## 📜 License

This project is open-sourced software licensed under the [MIT license](LICENSE).

## 👨‍💻 Developer

**Wildhan RY**
- GitHub: [@wildhanry](https://github.com/wildhanry)

## 📞 Support

Jika ada pertanyaan atau issue, silakan buat [GitHub Issue](https://github.com/wildhanry/simrs-admisi/issues).

---

**Built with ❤️ using Laravel & Tailwind CSS**
