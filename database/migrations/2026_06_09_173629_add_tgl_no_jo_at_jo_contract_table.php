<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b01_jo_cont', function (Blueprint $table) {
            $table->string('no_jo_cont', 30)->unique()->nullable()->after('id_jo_cont');
            $table->date('tgl_jo_cont')->nullable()->after('no_jo_cont');
        });
    }

    public function down(): void
    {
        Schema::table('b01_jo_cont', function (Blueprint $table) {
            $table->dropUnique(['no_jo_cont']);
            $table->dropColumn(['no_jo_cont', 'tgl_jo_cont']);
        });
    }
};