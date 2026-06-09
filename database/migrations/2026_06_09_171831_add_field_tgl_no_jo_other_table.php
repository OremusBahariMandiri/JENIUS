<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b05_jo_other', function (Blueprint $table) {
            $table->string('no_jo_other', 30)->unique()->nullable()->after('id_jo_other');
            $table->date('tgl_jo_other')->nullable()->after('no_jo_other');
        });
    }

    public function down(): void
    {
        Schema::table('b05_jo_other', function (Blueprint $table) {
            $table->dropUnique(['no_jo_other']);
            $table->dropColumn(['no_jo_other', 'tgl_jo_other']);
        });
    }
};