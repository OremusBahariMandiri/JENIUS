<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('f05_invoice_pembayaran_other', function (Blueprint $table) {
            $table->decimal('kurs_invoice', 20, 2)->nullable()->after('id_md_chart_of_account');
            $table->date('tgl_kurs_invoice')->nullable()->after('kurs_invoice');
        });

        Schema::table('f06_invoice_pembayaran_other_item', function (Blueprint $table) {
            $table->decimal('nominal_invoice_usd', 20, 4)->nullable()->after('nominal_invoice');
        });
    }

    public function down(): void
    {
        Schema::table('f05_invoice_pembayaran_other', function (Blueprint $table) {
            $table->dropColumn(['kurs_invoice', 'tgl_kurs_invoice']);
        });
        Schema::table('f06_invoice_pembayaran_other_item', function (Blueprint $table) {
            $table->dropColumn('nominal_invoice_usd');
        });
    }
};
