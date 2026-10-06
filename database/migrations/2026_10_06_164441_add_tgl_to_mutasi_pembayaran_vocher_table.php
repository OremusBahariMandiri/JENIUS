<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('e02_mutasi_pembayaran_voucher', function (Blueprint $table) {
            $table->date('tgl_keluar')->nullable()->after('keterangan');
            $table->string('id_md_chart_of_account', 50)->nullable()->after('tgl_keluar'); // ← ubah di sini
        });
    }

    public function down(): void
    {
        Schema::table('e02_mutasi_pembayaran_voucher', function (Blueprint $table) {
            $table->dropColumn(['tgl_keluar', 'id_md_chart_of_account']);
        });
    }
};
