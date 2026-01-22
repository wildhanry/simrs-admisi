# 🚀 Panduan Deploy SIMRS Admisi ke Render.com

## Prasyarat
- Akun Render.com
- Repository GitHub: https://github.com/wildhanry/simrs-admisi
- Database MySQL Railway (sudah ada)

## 📋 Langkah Deploy

### 1. Ganti Repository di Render

1. Login ke Render Dashboard
2. Pilih service yang sudah ada (Sistem Klinik)
3. Masuk ke **Settings** → **Build & Deploy**
4. Klik **Disconnect** repository lama
5. Klik **Connect** repository baru: `wildhanry/simrs-admisi`
6. Branch: `main`

### 2. Update Environment Variables

Masuk ke **Environment** tab, hapus semua variable lama dan tambahkan yang baru:

```env
APP_NAME=SIMRS Admisi
APP_ENV=production
APP_KEY=base64:wzf2TutABxOBExRRCfw6gK5gDzdFr3mHkgV4RpYxgmk=
APP_DEBUG=false
APP_TIMEZONE=Asia/Jakarta
APP_URL=https://simrs-admisi.onrender.com
APP_LOCALE=id
APP_FALLBACK_LOCALE=en

DB_CONNECTION=mysql
DB_HOST=tramway.proxy.rlwy.net
DB_PORT=45330
DB_DATABASE=railway
DB_USERNAME=root
DB_PASSWORD=omyAhHXodgEspcLflfJfDWhLUGRfjYby

CACHE_STORE=database
SESSION_DRIVER=database
SESSION_LIFETIME=120
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_LEVEL=error
MAIL_MAILER=log
FILESYSTEM_DISK=local
BCRYPT_ROUNDS=12

RUN_SEEDER=true
```

**⚠️ PENTING:** Set `RUN_SEEDER=true` hanya untuk deploy pertama!

### 3. Build Settings

**Build Command:**
```bash
composer install --no-dev --optimize-autoloader && npm ci && npm run build
```

**Start Command:**
```bash
bash render-entrypoint.sh
```

### 4. Deploy & Monitor

1. Klik **Manual Deploy** → **Deploy latest commit**
2. Monitor logs di **Logs** tab
3. Tunggu sampai muncul: `✅ Application ready!` dan `🌐 Starting Nginx on port...`
4. Service akan otomatis available di URL Render Anda

### 5. Post-Deployment

Setelah deploy pertama berhasil:

1. **Test Login:**
   - Admin: `admin@simrs.local` / `password`
   - Staff: `staff@simrs.local` / `password`

2. **Nonaktifkan Seeder:**
   - Ubah `RUN_SEEDER=false` di Environment Variables
   - Redeploy

3. **Ganti Password Default:**
   - Login sebagai admin
   - Ubah password semua user

## 🔐 Default Login

**Admin:**
- Email: `admin@simrs.local`
- Password: `password`

**Staff:**
- Email: `staff@simrs.local`
- Password: `password`

**⚠️ SEGERA ganti password setelah login pertama!**

## 📊 Data Awal Setelah Seeding

- ✅ 2 Users (1 admin, 1 staff)
- ✅ 5 Doctors dengan spesialisasi
- ✅ 5 Polyclinics (Umum, Anak, Kandungan, Jantung, Bedah)
- ✅ 4 Wards (VIP, Kelas 1, 2, 3)
- ✅ 50 Beds tersedia

## 🐛 Troubleshooting

### Error: "Port scan timeout reached"
**Penyebab:** Nginx tidak binding ke PORT yang benar
**Solusi:** 
- Pastikan menggunakan `bash render-entrypoint.sh` sebagai Start Command
- File `render-entrypoint.sh` harus ada di root project
- Check logs untuk memastikan Nginx start di port yang benar

### Error: "Database is unavailable - sleeping" (loop terus)
**Penyebab:** Script menunggu database Railway yang eksternal
**Solusi:** Sudah di-fix di `render-entrypoint.sh` terbaru - script akan skip database wait untuk external DB

### Error: "Base table or view not found"
**Penyebab:** Database belum di-migrate
**Solusi:** 
- Set `RUN_SEEDER=true` di environment variables
- Redeploy

### Error: "No application encryption key"
**Penyebab:** APP_KEY tidak di-set
**Solusi:** Set APP_KEY di environment variables

### Error: "SQLSTATE[HY000] [2002] Connection refused"
**Penyebab:** Credential database Railway salah atau database mati
**Solusi:** 
- Cek Railway dashboard apakah database masih running
- Verify DB_HOST, DB_PORT, DB_USERNAME, DB_PASSWORD benar

### Error: "Class not found" atau "No such file"
**Penyebab:** Build incomplete atau cache corrupt
**Solusi:**
- Clear build cache di Render → Settings → Clear build cache
- Redeploy

### Build gagal di "npm run build"
**Penyebab:** Dependencies tidak terinstall
**Solusi:** 
- Pastikan `package.json` dan `vite.config.js` ada di repository
- Check Render logs untuk error spesifik

## 📝 Checklist Deploy

- [ ] Repository terhubung ke Render
- [ ] Environment variables sudah diset lengkap
- [ ] Build command: `composer install --no-dev --optimize-autoloader && npm ci && npm run build`
- [ ] Start command: `bash render-entrypoint.sh`
- [ ] Database Railway masih aktif dan accessible
- [ ] Deploy berhasil tanpa error
- [ ] Aplikasi bisa diakses via URL Render
- [ ] Login admin/staff berhasil
- [ ] Test create patient
- [ ] Test pendaftaran rawat jalan
- [ ] Test pendaftaran rawat inap
- [ ] Test print PDF
- [ ] Password default sudah diganti
- [ ] `RUN_SEEDER=false` setelah deploy pertama

## 🔄 Update Aplikasi

Setiap kali ada update di GitHub:

1. Push ke repository `main` branch
2. Render otomatis detect dan deploy (jika auto-deploy enabled)
3. Atau klik **Manual Deploy** di Render dashboard
4. Monitor logs untuk memastikan deploy sukses
5. Test fitur yang diupdate

## 📞 Support

Jika ada masalah deployment:

1. **Check Render Logs** - Lihat error message detail
2. **Check Railway Database** - Pastikan masih online dan accessible
3. **Verify Environment Variables** - Pastikan semua required env vars ada
4. **Check Build Logs** - Cari error di build process
5. **Test Database Connection** - Via Render Shell: `php artisan db:show`

## 🔧 Advanced: Manual Commands via Render Shell

Buka Render Shell (di dashboard → Shell tab):

```bash
# Check database connection
php artisan db:show

# Run migrations manually
php artisan migrate --force

# Seed database manually
php artisan db:seed --force

# Clear cache
php artisan optimize:clear

# Check application status
php artisan about

# Check environment
php artisan env

# Run queue worker (if needed)
php artisan queue:work --stop-when-empty
```

## 🎯 Performance Tips

1. **Enable Caching:**
   - Config cache: `php artisan config:cache`
   - Route cache: `php artisan route:cache`
   - View cache: `php artisan view:cache`

2. **Optimize Autoloader:**
   - Sudah include di build command dengan `--optimize-autoloader`

3. **Use Database for Sessions:**
   - Sudah di-set dengan `SESSION_DRIVER=database`

4. **Monitor Logs:**
   - Set `LOG_LEVEL=error` untuk production
   - Gunakan `LOG_LEVEL=debug` hanya saat troubleshooting

---

**Selamat! SIMRS Admisi siap digunakan di Render.com! 🎉**
