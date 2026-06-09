<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b03_jo_tram', function (Blueprint $table) {
            $table->string('no_jo_tram', 30)->unique()->nullable()->after('id_jo_tram');
            $table->date('tgl_jo_tram')->nullable()->after('no_jo_tram');
        });
    }

    public function down(): void
    {
        Schema::table('b03_jo_tram', function (Blueprint $table) {
            $table->dropUnique(['no_jo_tram']);
            $table->dropColumn(['no_jo_tram', 'tgl_jo_tram']);
        });
    }
};