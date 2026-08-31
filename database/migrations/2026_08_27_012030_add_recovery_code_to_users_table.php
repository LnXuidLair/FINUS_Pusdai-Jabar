<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const RECOVERY_ALPHABET = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz!@#$%&*;';

    public function up(): void
    {
        if(! Schema::hasColumn('users', 'recovery_code')){
            Schema::table('users', function (Blueprint $table): void{
                $table->text('recovery_code')->nullable()->after('password');
            });
        }
        DB::table('users')
            ->where('role', 'admin')
            ->update(['email' => 'admin@pusdai.finus.id']);
        DB::table('users')
            ->where('role', 'pegawai')
            ->whereNotNull('email')
            ->update([
                'email' => DB::raw(
                    "CONCAT(SUBSTRING_INDEX(LOWER(email), '@', 1), '@staffpusdai.finus.id')"
                ),
            ]);
        if(Schema::hasTable('pegawai')){
            DB::table('pegawai')
                ->whereNotNull('email')
                ->update([
                    'email' => DB::raw(
                        "CONCAT(SUBSTRING_INDEX(LOWER(email), '@', 1), '@staffpusdai.finus.id')"
                    ),
                ]);
        }
        $usedCodes = [];
        $pegawaiUsers = DB::table('users')
            ->where('role', 'pegawai')
            ->orderBy('id')
            ->get(['id', 'recovery_code']);
        foreach ($pegawaiUsers as $user){
            $stored = trim((string)($user->recovery_code ?? ''));
            $plain = '';
            $mustRewrite = false;
            if($stored !== ''){
                try{
                    $plain = trim(Crypt::decryptString($stored));
                }catch(\Throwable){
                    $plain = trim($stored);
                    $mustRewrite = true;
                }
            }
            if($plain === '' || isset($usedCodes[$plain])){
                do{
                    $plain = $this->makeRecoveryCode();
                }while(isset($usedCodes[$plain]));
                $mustRewrite = true;
            }
            $usedCodes[$plain] = true;
            if($mustRewrite){
                DB::table('users')
                    ->where('id', $user->id)
                    ->update([
                        'recovery_code' => Crypt::encryptString($plain),
                    ]);
            }
        }
    }
    public function down(): void
    {
        if(Schema::hasColumn('users', 'recovery_code')){
            Schema::table('users', function (Blueprint $table): void{
                $table->dropColumn('recovery_code');
            });
        }
    }
    private function makeRecoveryCode(): string
    {
        $groups = [];
        for($group = 0; $group < 4; $group++){
            $part = '';
            for($char = 0; $char < 4; $char++){
                $part .= self::RECOVERY_ALPHABET[
                    random_int(0, strlen(self::RECOVERY_ALPHABET) - 1)
                ];
            }
            $groups[] = $part;
        }
        return 'FINUS-' . implode('-', $groups);
    }
};