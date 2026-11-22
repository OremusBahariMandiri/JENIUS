<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE f01_invoice_pembayaran_contract MODIFY COLUMN id_md_chart_of_account VARCHAR(255) NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE f01_invoice_pembayaran_contract MODIFY COLUMN id_md_chart_of_account BIGINT UNSIGNED NULL");
    }
};
