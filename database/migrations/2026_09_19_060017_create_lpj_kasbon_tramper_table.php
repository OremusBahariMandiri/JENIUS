<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d05_lpj_kasbon_tram', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_kasbon_tram')->unique();
            $table->string('id_lpj_tram');
            $table->foreign('id_lpj_tram')->references('id_lpj_tram')->on('d04_lpj_tram');
            $table->string('id_kasbon_tram');
            $table->foreign('id_kasbon_tram')->references('id_kasbon_tram')->on('c03_kasbon_tram');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d05_lpj_kasbon_tram');
    }
};