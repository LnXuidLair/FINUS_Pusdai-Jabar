# FINUS - Sistem Informasi Keuangan Masjid

Proyek Laravel 10 dengan tiga role:

- `admin`: maksimal satu akun.
- `pegawai`: akun diaktifkan memakai NIP dan memiliki jabatan masjid.
- `jamaah`: wajib memakai alamat `@gmail.com` dan memverifikasi email.

## Instalasi

```bash
composer install
npm install
cp .env.example .env
php artisan key:generate
php artisan storage:link
php artisan migrate
npm run build
php artisan serve
```

Isi koneksi database, `ACCESS_CODE_ADMIN`, `ACCESS_CODE_STAFF`, dan SMTP Gmail
di `.env`. Untuk Gmail, gunakan Google App Password.

## Keamanan sesi

Sesi dapat digunakan pada banyak tab. Aktivitas antartab disinkronkan melalui
`localStorage`; jika seluruh tab tidak aktif selama 15 menit, akun otomatis
logout. Middleware server juga memutus sesi yang tidak aktif.

## Catatan

Folder `vendor` dan `node_modules` sengaja tidak disertakan karena dibuat oleh
`composer install` dan `npm install`.
