<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d08_lpj_kasbon_other', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_kasbon_other')->unique();
            $table->string('id_lpj_other');
            $table->foreign('id_lpj_other')->references('id_lpj_other')->on('d07_lpj_other');
            $table->string('id_kasbon_other');
            $table->foreign('id_kasbon_other')->references('id_kasbon_other')->on('c05_kasbon_other');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d08_lpj_kasbon_other');
    }
};