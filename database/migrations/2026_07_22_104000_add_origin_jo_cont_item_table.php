<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b02_jo_cont_item', function (Blueprint $table) {
            $table->string('origin_lpj_cont')->nullable()->after('note');
            $table->foreign('origin_lpj_cont')->references('id_lpj_cont')->on('d01_lpj_cont')->nullOnDelete();
        });

        Schema::table('c02_kasbon_cont_item', function (Blueprint $table) {
            $table->string('origin_lpj_cont')->nullable()->after('total_kasbon');
            $table->foreign('origin_lpj_cont')->references('id_lpj_cont')->on('d01_lpj_cont')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('b02_jo_cont_item', function (Blueprint $table) {
            $table->dropForeign(['origin_lpj_cont']);
            $table->dropColumn('origin_lpj_cont');
        });

        Schema::table('c02_kasbon_cont_item', function (Blueprint $table) {
            $table->dropForeign(['origin_lpj_cont']);
            $table->dropColumn('origin_lpj_cont');
        });
    }
};