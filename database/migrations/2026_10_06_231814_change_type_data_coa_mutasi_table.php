<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('e01_mutasi_pembayaran', function (Blueprint $table) {
            $table->string('id_md_chart_of_account')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('e01_mutasi_pembayaran', function (Blueprint $table) {
            $table->string('id_md_chart_of_account')->nullable(false)->change();
        });
    }
};