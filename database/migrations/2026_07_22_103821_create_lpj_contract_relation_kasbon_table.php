<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d02_lpj_kasbon', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_kasbon')->unique();
            $table->string('id_lpj_cont');
            $table->foreign('id_lpj_cont')->references('id_lpj_cont')->on('d01_lpj_cont');
            $table->string('id_kasbon_cont');
            $table->foreign('id_kasbon_cont')->references('id_kasbon_cont')->on('c01_kasbon_cont');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d02_lpj_kasbon');
    }
};