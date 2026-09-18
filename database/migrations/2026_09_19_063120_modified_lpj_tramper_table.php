<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b04_jo_tram_item', function (Blueprint $table) {
            $table->string('origin_lpj_tram')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('b04_jo_tram_item', function (Blueprint $table) {
            $table->dropColumn('origin_lpj_tram');
        });
    }
};