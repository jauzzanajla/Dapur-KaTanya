# Dapur KaTanya

Aplikasi pemesanan makanan dan minuman Dapur KaTanya, dibangun dengan Laravel, PostgreSQL, dan Vite.

## Menjalankan secara lokal

```bash
composer install
copy .env.example .env
php artisan key:generate
php artisan migrate
npm install
npm run build
php artisan serve
```

Atur koneksi database di `.env` sebelum menjalankan migrasi. Jangan commit file `.env` atau data pelanggan.

## Deploy ke Vercel

Aplikasi menggunakan `Dockerfile`, FrankenPHP, dan `Caddyfile`. Sebelum deploy, atur `APP_KEY`, `APP_URL`, dan kredensial PostgreSQL produksi di Environment Variables Vercel, lalu jalankan migrasi pada database produksi.

Filesystem container Vercel tidak persisten. Atur disk `public` ke object storage S3-compatible untuk QRIS, gambar menu, dan bukti pembayaran sebelum menerima upload produksi.

```bash
vercel deploy --prod
```
