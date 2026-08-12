<?php

namespace Database\Seeders;

use App\Models\AgendaKegiatan;
use Illuminate\Database\Seeder;

class AgendaKegiatanSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama agar tidak duplikat
        AgendaKegiatan::truncate();

        $data = [
            [
                'judul'     => 'Kajian Rutin Subuh',
                'kategori'  => 'kajian',
                'hari'      => 'Setiap Ahad',
                'waktu'     => '05.15 - 06.30 WIB',
                'lokasi'    => 'Aula Masjid Pusdai',
                'deskripsi' => 'Kajian pekanan untuk jamaah umum setelah salat Subuh.',
                'is_aktif'  => true,
                'urutan'    => 1,
            ],
            [
                'judul'     => 'Jumat Berkah',
                'kategori'  => 'sosial',
                'hari'      => 'Setiap Jumat',
                'waktu'     => '11.00 - selesai',
                'lokasi'    => 'Area Masjid',
                'deskripsi' => 'Pembagian konsumsi dan sedekah untuk jamaah Jumat.',
                'is_aktif'  => true,
                'urutan'    => 2,
            ],
            [
                'judul'     => "Kelas Tahsin Al-Qur'an",
                'kategori'  => 'pendidikan',
                'hari'      => 'Setiap Sabtu',
                'waktu'     => '16.00 - 17.30 WIB',
                'lokasi'    => 'Ruang Belajar',
                'deskripsi' => "Kegiatan belajar memperbaiki bacaan Al-Qur'an.",
                'is_aktif'  => true,
                'urutan'    => 3,
            ],
        ];

        foreach ($data as $item) {
            AgendaKegiatan::create($item);
        }
    }
}
