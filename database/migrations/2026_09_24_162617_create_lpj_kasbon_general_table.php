<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d11_lpj_gen_kasbon', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_gen_kasbon', 50)->unique();
            $table->string('id_lpj_gen', 50);
            $table->string('id_kasbon_gen', 50);
            $table->timestamps();

            $table->foreign('id_lpj_gen')
                ->references('id_lpj_gen')
                ->on('d10_lpj_gen')
                ->onDelete('cascade');

            $table->foreign('id_kasbon_gen')
                ->references('id_kasbon_gen')
                ->on('c07_kasbon_gen')
                ->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d11_lpj_gen_kasbon');
    }
};