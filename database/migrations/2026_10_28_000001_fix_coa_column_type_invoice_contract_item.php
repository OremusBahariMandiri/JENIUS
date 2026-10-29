<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Drop FK only if it actually exists
        $fkExists = DB::select("
            SELECT CONSTRAINT_NAME
            FROM information_schema.TABLE_CONSTRAINTS
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME   = 'f02_invoice_pembayaran_contract_item'
              AND CONSTRAINT_NAME = 'f02_invoice_pembayaran_contract_item_id_md_chart_of_account_foreign'
              AND CONSTRAINT_TYPE = 'FOREIGN KEY'
        ");

        if (!empty($fkExists)) {
            Schema::table('f02_invoice_pembayaran_contract_item', function (Blueprint $table) {
                $table->dropForeign(['id_md_chart_of_account']);
            });
        }

        Schema::table('f02_invoice_pembayaran_contract_item', function (Blueprint $table) {
            $table->string('id_md_chart_of_account', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('f02_invoice_pembayaran_contract_item', function (Blueprint $table) {
            $table->unsignedBigInteger('id_md_chart_of_account')->nullable()->change();
        });
    }
};
