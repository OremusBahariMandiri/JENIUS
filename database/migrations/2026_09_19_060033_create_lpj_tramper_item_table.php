<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d06_lpj_tram_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_tram_item')->unique();
            $table->string('id_lpj_tram');
            $table->foreign('id_lpj_tram')->references('id_lpj_tram')->on('d04_lpj_tram');
            $table->string('id_kasbon_tram_item')->nullable();
            $table->foreign('id_kasbon_tram_item')->references('id_kasbon_tram_item')->on('c04_kasbon_tram_item');
            $table->string('id_jo_tram_item')->nullable();
            $table->foreign('id_jo_tram_item')->references('id_jo_tram_item')->on('b04_jo_tram_item');
            $table->decimal('amount_lpj', 15, 2)->nullable();
            $table->string('id_md_chart_of_account')->nullable();
            $table->foreign('id_md_chart_of_account')->references('id_md_chart_of_account')->on('a13_md_chart_of_account');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d06_lpj_tram_item');
    }
};
