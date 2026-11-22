<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('a15_company_profile', function (Blueprint $table) {
            $table->string('nama_ttd_kwitansi')->nullable()->after('alamat');
            $table->string('jabatan_ttd_kwitansi')->nullable()->after('nama_ttd_kwitansi');
        });
    }

    public function down(): void
    {
        Schema::table('a15_company_profile', function (Blueprint $table) {
            $table->dropColumn(['nama_ttd_kwitansi', 'jabatan_ttd_kwitansi']);
        });
    }
};
