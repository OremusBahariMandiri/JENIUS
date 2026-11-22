<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE f06_invoice_pembayaran_other_item MODIFY id_jo_other_item VARCHAR(50) NULL DEFAULT NULL');
    }

    public function down(): void
    {
        // intentionally left empty
    }
};
