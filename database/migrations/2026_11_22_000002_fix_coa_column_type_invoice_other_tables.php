<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("SET FOREIGN_KEY_CHECKS=0");
        DB::statement("ALTER TABLE f05_invoice_pembayaran_other MODIFY COLUMN id_md_chart_of_account VARCHAR(255) NULL");
        DB::statement("ALTER TABLE f06_invoice_pembayaran_other_item MODIFY COLUMN id_md_chart_of_account VARCHAR(255) NULL");
        DB::statement("SET FOREIGN_KEY_CHECKS=1");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE f05_invoice_pembayaran_other MODIFY COLUMN id_md_chart_of_account BIGINT UNSIGNED NULL");
        DB::statement("ALTER TABLE f06_invoice_pembayaran_other_item MODIFY COLUMN id_md_chart_of_account BIGINT UNSIGNED NULL");
    }
};
