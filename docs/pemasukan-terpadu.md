# Pemasukan terpadu

Menu Transaksi ZISWAF admin digabung ke Pemasukan. Tidak ada migrasi atau pemindahan data: keduanya sejak awal menggunakan ziswaf_penerimaan. Halaman jamaah dan proses konfirmasi gateway tetap memakai alur yang ada.

Tab Semua Penerimaan memuat seluruh status, termasuk dibatalkan. Menunggu Verifikasi hanya memuat pembayaran manual yang masih pending (atau status lama null). Riwayat Pembayaran memuat status terminal verifikasi/pembayaran. Filter golongan, pencarian, dan status mengikuti tab dan pagination. Total diterima tetap hanya menjumlah penerimaan diterima, bukan semua pesanan pembayaran.

Daftar menampilkan asal Jamaah/Admin/Penanggung Jawab Shift, order, kategori penerimaan, peruntukan golongan, metode, akun Kas 1101 atau Bank 1102, status pembayaran/verifikasi, serta nomor referensi jurnal aktual. Kategori tambahan historis tampil pada filter tanpa menambah pilihan penerimaan manual baru.

Endpoint lama admin.ziswaf.transaksi.index mengarahkan ke Pemasukan dengan pemetaan filter jenis menjadi golongan. Endpoint verifikasi lama memakai handler Pemasukan yang sama. Verifikasi manual berjalan di transaksi database dengan kunci record, menolak verifikasi ulang/gateway dan membatalkan perubahan status bila jurnal gagal. Notifikasi baru mengarah ke tab Pemasukan yang sesuai.

Verifikasi: 29 tes, 190 assertions lulus menggunakan database finus_cash_transfer_testing terpisah, melalui phpunit.cash-transfer.xml dengan filter UnifiedPemasukanTest|CashTransferTest|MasterCoaPengeluaranTest. Tidak ada data aplikasi yang dipakai untuk tes.
