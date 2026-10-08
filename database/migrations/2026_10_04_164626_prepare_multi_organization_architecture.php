<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('organizations')) {
            Schema::create('organizations', function (Blueprint $table): void {
                $table->id();
                $table->string('public_id', 26)->unique();
                $table->string('name');
                $table->string('slug')->unique();
                $table->string('legal_name')->nullable();
                $table->text('address')->nullable();
                $table->string('village')->nullable();
                $table->string('district')->nullable();
                $table->string('city')->nullable();
                $table->string('province')->nullable();
                $table->string('postal_code', 20)->nullable();
                $table->string('country_code', 2)->default('ID');
                $table->string('phone', 20)->nullable();
                $table->string('email')->nullable();
                $table->string('logo_path')->nullable();
                $table->text('description')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('organization_settings')) {
            Schema::create('organization_settings', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->unique()->constrained('organizations')->cascadeOnDelete();
                $table->string('timezone', 64)->default('Asia/Jakarta');
                $table->string('currency', 3)->default('IDR');
                $table->boolean('zakat_enabled')->default(true);
                $table->boolean('infaq_enabled')->default(true);
                $table->boolean('wakaf_enabled')->default(true);
                $table->json('settings')->nullable();
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('organization_payment_channels')) {
            Schema::create('organization_payment_channels', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('organization_id')->constrained('organizations')->cascadeOnDelete();
                $table->string('type', 40);
                $table->string('provider')->nullable();
                $table->string('account_name')->nullable();
                $table->string('account_number')->nullable();
                $table->string('qr_image_path')->nullable();
                $table->boolean('is_active')->default(true)->index();
                $table->json('metadata')->nullable();
                $table->timestamps();

                $table->index(['organization_id', 'type', 'is_active'], 'org_payment_channel_lookup');
            });
        }

        $this->addOrganizationColumns();
        $organizationId = $this->ensureLegacyOrganization();

        if ($organizationId) {
            $this->backfillOrganizationData($organizationId);
        }
    }

    public function down(): void
    {
        $tables = [
            'periode_penyaluran_zakat',
            'parkir_sesi',
            'agenda_kegiatan',
            'ziswaf_penyaluran',
            'ziswaf_penerimaan',
            'pengeluaran',
            'penggajian',
            'presensi',
            'jurnal_umum',
            'coa',
            'gaji_jabatan_riwayat',
            'gaji_jabatan',
            'pegawai',
            'users',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName) || ! Schema::hasColumn($tableName, 'organization_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table) use ($tableName): void {
                try {
                    $table->dropConstrainedForeignId('organization_id');
                } catch (Throwable) {
                    $table->dropColumn('organization_id');
                }
            });
        }

        Schema::dropIfExists('organization_payment_channels');
        Schema::dropIfExists('organization_settings');
        Schema::dropIfExists('organizations');
    }

    private function addOrganizationColumns(): void
    {
        $tables = [
            'users',
            'pegawai',
            'gaji_jabatan',
            'gaji_jabatan_riwayat',
            'coa',
            'jurnal_umum',
            'presensi',
            'penggajian',
            'pengeluaran',
            'ziswaf_penerimaan',
            'ziswaf_penyaluran',
            'agenda_kegiatan',
            'parkir_sesi',
            'periode_penyaluran_zakat',
        ];

        foreach ($tables as $tableName) {
            if (! Schema::hasTable($tableName) || Schema::hasColumn($tableName, 'organization_id')) {
                continue;
            }

            Schema::table($tableName, function (Blueprint $table): void {
                $table->foreignId('organization_id')
                    ->nullable()
                    ->constrained('organizations')
                    ->nullOnDelete();
            });
        }
    }

    private function ensureLegacyOrganization(): ?int
    {
        $existing = DB::table('organizations')->orderBy('id')->first();
        if ($existing) {
            DB::table('organization_settings')->updateOrInsert(
                ['organization_id' => $existing->id],
                [
                    'timezone' => 'Asia/Jakarta',
                    'currency' => 'IDR',
                    'zakat_enabled' => true,
                    'infaq_enabled' => true,
                    'wakaf_enabled' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );

            return (int) $existing->id;
        }

        if (! Schema::hasTable('users')) {
            return null;
        }

        $adminQuery = DB::table('users')->where('role', 'admin')->orderBy('id');
        $columns = ['id', 'name', 'email'];
        if (Schema::hasColumn('users', 'nama_masjid')) {
            $columns[] = 'nama_masjid';
        }

        $admin = $adminQuery->first($columns);
        if (! $admin) {
            return null;
        }

        $name = trim((string) ($admin->nama_masjid ?? ''));
        if ($name === '') {
            $name = preg_replace('/^Admin\s+/iu', '', trim((string) $admin->name)) ?: 'PUSDAI Jabar';
        }

        $slugBase = Str::slug($name) ?: 'masjid';
        $slug = $slugBase;
        $suffix = 2;
        while (DB::table('organizations')->where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $suffix++;
        }

        $now = now();
        $id = DB::table('organizations')->insertGetId([
            'public_id' => (string) Str::ulid(),
            'name' => $name,
            'slug' => $slug,
            'country_code' => 'ID',
            'is_active' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('organization_settings')->insert([
            'organization_id' => $id,
            'timezone' => 'Asia/Jakarta',
            'currency' => 'IDR',
            'zakat_enabled' => true,
            'infaq_enabled' => true,
            'wakaf_enabled' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        return (int) $id;
    }

    private function backfillOrganizationData(int $organizationId): void
    {
        if (Schema::hasTable('users') && Schema::hasColumn('users', 'organization_id')) {
            DB::table('users')
                ->whereIn('role', ['admin', 'pegawai'])
                ->whereNull('organization_id')
                ->update(['organization_id' => $organizationId]);

            // Jamaah sengaja TIDAK diberi organization_id. Akun Jamaah bersifat
            // publik/global dan dapat bertransaksi ke banyak masjid.
            DB::table('users')
                ->where('role', 'jamaah')
                ->update(['organization_id' => null]);
        }

        $ownedTables = [
            'pegawai',
            'gaji_jabatan',
            'gaji_jabatan_riwayat',
            'coa',
            'jurnal_umum',
            'presensi',
            'penggajian',
            'pengeluaran',
            'ziswaf_penerimaan',
            'ziswaf_penyaluran',
            'agenda_kegiatan',
            'parkir_sesi',
            'periode_penyaluran_zakat',
        ];

        foreach ($ownedTables as $tableName) {
            if (Schema::hasTable($tableName) && Schema::hasColumn($tableName, 'organization_id')) {
                DB::table($tableName)
                    ->whereNull('organization_id')
                    ->update(['organization_id' => $organizationId]);
            }
        }
    }
};
