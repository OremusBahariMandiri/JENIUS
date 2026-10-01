<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('c08_kasbon_gen_item', function (Blueprint $table) {
            $table->string('origin_lpj_gen', 50)
                ->nullable()
                ->after('nilai_kasbon')
                ->comment('Diisi jika item ini dibuat dari halaman LPJ General (bukan dari kasbon existing)');
        });
    }

    public function down(): void
    {
        Schema::table('c08_kasbon_gen_item', function (Blueprint $table) {
            $table->dropColumn('origin_lpj_gen');
        });
    }
};