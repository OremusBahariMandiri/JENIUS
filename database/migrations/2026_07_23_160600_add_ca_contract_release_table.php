<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('c01_kasbon_cont', function (Blueprint $table) {
            // Status: 'pending' (default) atau 'released'
            $table->string('ca_release_status', 20)->default('pending')->after('tgl_release');
            // Tanggal release CA (wajib diisi jika status = released)
            $table->date('ca_release_date')->nullable()->after('ca_release_status');
        });

        // Pastikan semua baris lama terisi nilai default
        DB::table('c01_kasbon_cont')
            ->whereNull('ca_release_status')
            ->update(['ca_release_status' => 'pending']);
    }

    public function down(): void
    {
        Schema::table('c01_kasbon_cont', function (Blueprint $table) {
            $table->dropColumn(['ca_release_status', 'ca_release_date']);
        });
    }
};