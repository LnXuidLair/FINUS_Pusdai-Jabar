<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('ziswaf_penerimaan')) {
            return;
        }

        DB::statement("
            ALTER TABLE ziswaf_penerimaan
            MODIFY jenis_ziswaf VARCHAR(50) NOT NULL
        ");

        DB::statement("
            ALTER TABLE ziswaf_penerimaan
            MODIFY metode_pembayaran VARCHAR(50) NOT NULL DEFAULT 'manual_transfer'
        ");

        Schema::table('ziswaf_penerimaan', function (Blueprint $table) {
            if (!Schema::hasColumn('ziswaf_penerimaan', 'order_id')) {
                $table->string('order_id')->nullable()->unique()->after('id');
            }

            if (!Schema::hasColumn('ziswaf_penerimaan', 'snap_token')) {
                $table->text('snap_token')->nullable()->after('order_id');
            }

            if (!Schema::hasColumn('ziswaf_penerimaan', 'payment_gateway')) {
                $table->string('payment_gateway', 50)->nullable()->after('metode_pembayaran');
            }

            if (!Schema::hasColumn('ziswaf_penerimaan', 'payment_status')) {
                $table->string('payment_status', 50)->default('manual_pending')->after('payment_gateway');
            }

            if (!Schema::hasColumn('ziswaf_penerimaan', 'payment_type')) {
                $table->string('payment_type', 100)->nullable()->after('payment_status');
            }

            if (!Schema::hasColumn('ziswaf_penerimaan', 'transaction_id')) {
                $table->string('transaction_id')->nullable()->after('payment_type');
            }

            if (!Schema::hasColumn('ziswaf_penerimaan', 'fraud_status')) {
                $table->string('fraud_status', 50)->nullable()->after('transaction_id');
            }

            if (!Schema::hasColumn('ziswaf_penerimaan', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('verified_at');
            }
        });
    }

    public function down(): void
    {
        if (!Schema::hasTable('ziswaf_penerimaan')) {
            return;
        }

        Schema::table('ziswaf_penerimaan', function (Blueprint $table) {
            $columns = [];

            foreach ([
                'order_id',
                'snap_token',
                'payment_gateway',
                'payment_status',
                'payment_type',
                'transaction_id',
                'fraud_status',
                'paid_at',
            ] as $column) {
                if (Schema::hasColumn('ziswaf_penerimaan', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};