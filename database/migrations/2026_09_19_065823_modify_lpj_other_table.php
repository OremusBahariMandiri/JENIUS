<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b06_jo_other_item', function (Blueprint $table) {
            $table->string('origin_lpj_other')->nullable()->after('note');
        });
    }

    public function down(): void
    {
        Schema::table('b06_jo_other_item', function (Blueprint $table) {
            $table->dropColumn('origin_lpj_other');
        });
    }
};