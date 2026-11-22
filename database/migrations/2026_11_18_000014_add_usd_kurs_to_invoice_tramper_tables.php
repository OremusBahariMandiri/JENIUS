<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('f03_invoice_pembayaran_tramper', function (Blueprint $table) {
            $table->decimal('kurs_invoice', 20, 2)->nullable()->after('currency');
            $table->date('tgl_kurs_invoice')->nullable()->after('kurs_invoice');
        });

        Schema::table('f04_invoice_pembayaran_tramper_item', function (Blueprint $table) {
            $table->decimal('nominal_invoice_usd', 20, 4)->nullable()->default(0)->after('nominal_invoice');
        });
    }

    public function down(): void
    {
        Schema::table('f03_invoice_pembayaran_tramper', function (Blueprint $table) {
            $table->dropColumn(['kurs_invoice', 'tgl_kurs_invoice']);
        });
        Schema::table('f04_invoice_pembayaran_tramper_item', function (Blueprint $table) {
            $table->dropColumn('nominal_invoice_usd');
        });
    }
};
