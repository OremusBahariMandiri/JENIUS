<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Fix id_jo_other: unsignedBigInteger → string (nilai aktual seperti 'B05112600002')
        DB::statement('ALTER TABLE f05_invoice_pembayaran_other MODIFY id_jo_other VARCHAR(50) NOT NULL');

        // Fix id_jo_other_item & id_jo_other_pajak di tabel item (keduanya string)
        DB::statement('ALTER TABLE f06_invoice_pembayaran_other_item MODIFY id_jo_other_item VARCHAR(50) NOT NULL');
        DB::statement('ALTER TABLE f06_invoice_pembayaran_other_item MODIFY id_jo_other_pajak VARCHAR(50) NULL');
    }

    public function down(): void
    {
        // Revert — hanya bisa kalau kolom tidak berisi nilai non-numeric
        DB::statement('ALTER TABLE f05_invoice_pembayaran_other MODIFY id_jo_other BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE f06_invoice_pembayaran_other_item MODIFY id_jo_other_item BIGINT UNSIGNED NOT NULL');
        DB::statement('ALTER TABLE f06_invoice_pembayaran_other_item MODIFY id_jo_other_pajak BIGINT UNSIGNED NULL');
    }
};
