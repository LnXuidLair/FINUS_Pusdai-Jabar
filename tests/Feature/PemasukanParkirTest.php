<?php

namespace Tests\Feature;

use App\Models\Pegawai;
use App\Models\User;
use App\Models\ZiswafPenerimaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class PemasukanParkirTest extends TestCase
{
    use RefreshDatabase;

    public function test_parking_position_is_a_general_employee_and_dedicated_parking_routes_are_removed(): void
    {
        $pegawai = $this->pegawai('PKR-001', 'parkir-role@finus.test', 'Petugas Parkir');
        User::create([
            'name' => $pegawai->nama_pegawai,
            'email' => $pegawai->email,
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_PEGAWAI,
        ]);

        $this->assertSame(Pegawai::AKSES_UMUM, $pegawai->akses_role);
        $this->assertFalse(Route::has('admin.parkir.index'));
        $this->assertFalse(Route::has('pegawai.parkir.index'));
        $this->assertFalse(Route::has('pegawai.parkir.masuk'));
        $this->assertFalse(Route::has('pegawai.parkir.keluar'));
        $this->assertFalse(Route::has('admin.pemasukan.parkir.store'));
        $this->assertFalse(Route::has('pegawai.keuangan.pemasukan.parkir.store'));
        $this->assertTrue(Route::has('admin.pemasukan.store'));
        $this->assertFalse(Schema::hasTable('parkir_sesi'));
        $this->assertFalse(Schema::hasTable('parkir_aktivitas'));
        $this->assertFalse(Schema::hasTable('pemasukan_parkir_details'));
    }

    public function test_admin_records_parking_shift_as_mosque_income_with_responsible_employee(): void
    {
        $admin = $this->admin();
        $pegawai = $this->pegawai('PKR-002', 'shift-owner@finus.test', 'Petugas Parkir');
        Storage::fake('public');

        $this->actingAs($admin, 'admin')
            ->post(route('admin.pemasukan.store'), [
                'jenis_ziswaf' => 'parkir',
                'id_pegawai' => $pegawai->id,
                'tanggal' => now()->toDateString(),
                'shift' => 'pagi',
                'nominal' => 410000,
                'metode_pembayaran' => 'tunai',
                'nomor_rekap' => 'PKR-TEST-001',
                'keterangan' => 'Diserahkan kepada bagian keuangan.',
                'bukti_pembayaran' => UploadedFile::fake()->create(
                    'laporan-parkir.pdf',
                    100,
                    'application/pdf'
                ),
            ])
            ->assertRedirect(route('admin.pemasukan.index'))
            ->assertSessionHas('success');

        $pemasukan = ZiswafPenerimaan::where('jenis_ziswaf', 'parkir')->firstOrFail();

        $this->assertSame($pegawai->id, $pemasukan->id_pegawai);
        $this->assertSame(410000, $pemasukan->nominal);
        $this->assertNotNull($pemasukan->jurnal_id);
        Storage::disk('public')->assertExists($pemasukan->bukti_pembayaran);
        $this->assertSame('pagi', $pemasukan->rincian_perhitungan['shift']);
        $this->assertSame('06:00', $pemasukan->rincian_perhitungan['waktu_mulai']);
        $this->assertSame('14:00', $pemasukan->rincian_perhitungan['waktu_selesai']);
        $this->assertSame('PKR-TEST-001', $pemasukan->rincian_perhitungan['nomor_rekap']);
        $this->assertDatabaseHas('jurnal_detail', [
            'jurnal_id' => $pemasukan->jurnal_id,
            'jenis_dana' => 'operasional',
            'psak_reference' => null,
            'debit' => 410000,
        ]);
    }

    public function test_parking_is_selected_inside_the_general_income_form(): void
    {
        $this->actingAs($this->admin(), 'admin')
            ->get(route('admin.pemasukan.index'))
            ->assertOk()
            ->assertSee('onclick="pmOpenTambah()"', false)
            ->assertDontSee('pmOpenPilihan', false)
            ->assertDontSee('pmOpenParkir', false)
            ->assertSee('<option value="parkir"', false)
            ->assertSee('id="pmParkingFields" hidden', false)
            ->assertSeeText('Penanggung Jawab Shift')
            ->assertSeeText('Nomor Laporan / Rekap')
            ->assertSee('Bukti Laporan Parkir', false);
    }

    public function test_parking_income_requires_shift_employee_and_report_proof(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin, 'admin')
            ->from(route('admin.pemasukan.index'))
            ->post(route('admin.pemasukan.store'), [
                'jenis_ziswaf' => 'parkir',
                'tanggal' => now()->toDateString(),
                'nominal' => 100000,
                'metode_pembayaran' => 'tunai',
            ])
            ->assertRedirect(route('admin.pemasukan.index'))
            ->assertSessionHasErrors(['id_pegawai', 'shift', 'bukti_pembayaran']);

        $this->assertDatabaseMissing('ziswaf_penerimaan', ['jenis_ziswaf' => 'parkir']);
    }

    private function admin(): User
    {
        return User::create([
            'name' => 'Admin Pemasukan',
            'email' => 'admin-parkir-'.uniqid().'@finus.test',
            'email_verified_at' => now(),
            'password' => bcrypt('password123'),
            'role' => User::ROLE_ADMIN,
        ]);
    }

    private function pegawai(string $nip, string $email, string $jabatan): Pegawai
    {
        return Pegawai::create([
            'nip' => $nip,
            'nama_pegawai' => 'Petugas '.str_replace('-', ' ', $nip),
            'jabatan' => $jabatan,
            'email' => $email,
            'is_verified' => true,
        ]);
    }
}
