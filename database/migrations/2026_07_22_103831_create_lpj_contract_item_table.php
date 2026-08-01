<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d03_lpj_cont_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_cont_item')->unique();

            $table->string('id_lpj_cont');
            $table->foreign('id_lpj_cont')
                ->references('id_lpj_cont')
                ->on('d01_lpj_cont')
                ->onDelete('cascade');

            $table->string('id_kasbon_cont_item');
            $table->foreign('id_kasbon_cont_item')
                ->references('id_kasbon_cont_item')
                ->on('c02_kasbon_cont_item')
                ->onDelete('cascade');

            $table->string('id_jo_cont_item');
            $table->foreign('id_jo_cont_item')
                ->references('id_jo_cont_item')
                ->on('b02_jo_cont_item')
                ->onDelete('cascade');

            $table->decimal('amount_lpj', 18, 2)->default(0);

            $table->timestamps();

            $table->index('id_lpj_cont');
            $table->index('id_kasbon_cont_item');
            $table->index('id_jo_cont_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d03_lpj_cont_item');
    }
};