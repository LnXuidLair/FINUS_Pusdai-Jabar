# Pemindahan Kas–Bank

Fitur tambahan tanpa migrasi atau perubahan data penerimaan lama. Menu khusus admin pada Keuangan. Pemindahan memakai jurnal sumber kas_bank_transfer dan pembatalan kas_bank_reversal. Keduanya dikecualikan dari scope laporan pemasukan/pengeluaran; tetap tampil di jurnal umum dan saldo.

Nonaktifkan dengan CASH_TRANSFERS_ENABLED=false di .env lalu jalankan php artisan config:clear. Menu, endpoint, tautan dan validasi tambahan pengeluaran dinonaktifkan. Data jurnal tetap berlaku agar saldo sesuai uang sebenarnya.

Untuk mengembalikan saldo sebelum percobaan, batalkan pemindahan dari menu SEBELUM menonaktifkan fitur. Pembatalan membuat jurnal balik dan menolak jika saldo tujuan sudah terpakai. Jangan menghapus jurnal langsung. Fitur tidak mengubah pemetaan kategori atau jurnal lama. Riwayat pembatalan tetap disimpan untuk audit.

File tambahan: config/cash_transfers.php, CashTransferController, CashTransferService, view admin/cash-transfers, dan tes CashTransferTest. Integrasi terbatas pada route tambahan, sidebar admin, tautan form pengeluaran, validasi pada transaksi pengeluaran, dan pengecualian scope JurnalUmum.

Saldo lokasi per golongan memakai jenis_dana pada jurnal kas/bank. Alokasi historis yang hanya mencatat akun pendapatan/beban belum menentukan lokasi uang; selisihnya harus direkonsiliasi, bukan diperbaiki otomatis. Penggajian tetap memakai alur sebelumnya. Pembayaran gabungan dapat dicatat sebagai transaksi terpisah sesuai pembayaran aktual; form ini tidak menambah mode gabungan.

## Pengujian

Database khusus `finus_cash_transfer_testing` dipakai karena database testing lama mengalami konflik tablespace. Jangan gunakan database aplikasi untuk RefreshDatabase.

Jalankan `php vendor/phpunit/phpunit/phpunit -c phpunit.cash-transfer.xml --filter "CashTransferTest|MasterCoaPengeluaranTest"`.

Hasil verifikasi: 25 tes, 146 assertions lulus, termasuk rendering halaman dengan aset Vite, pemindahan, pembatalan berulang, pembatalan setelah saldo terpakai, sumber salah golongan, isolasi organisasi, pengeluaran dari sumber benar dan penonaktifan aturan tambahan.

Dependensi notifikasi lokal yang hilang dipulihkan sesuai versi dan checksum package-lock, lalu aset notifikasi dan manifest lokal dibangun tanpa mengubah sumber notifikasi atau aset lama. `public/build` dan `node_modules` diabaikan Git; pada lingkungan lain jalankan instalasi dependensi dan build Vite seperti biasa.
