<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('e01_mutasi_pembayaran', function (Blueprint $table) {
            $table->string('mata_uang', 10)->default('IDR')->after('kurs');
            $table->date('tgl_kurs')->nullable()->after('mata_uang');
        });
    }

    public function down(): void
    {
        Schema::table('e01_mutasi_pembayaran', function (Blueprint $table) {
            $table->dropColumn(['mata_uang', 'tgl_kurs']);
        });
    }
};
