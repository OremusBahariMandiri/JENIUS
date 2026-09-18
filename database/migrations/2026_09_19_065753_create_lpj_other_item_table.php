<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d09_lpj_other_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_other_item')->unique();
            $table->string('id_lpj_other');
            $table->foreign('id_lpj_other')->references('id_lpj_other')->on('d07_lpj_other');
            $table->string('id_kasbon_other_item')->nullable();
            $table->foreign('id_kasbon_other_item')->references('id_kasbon_other_item')->on('c06_kasbon_other_item');
            $table->string('id_jo_other_item')->nullable();
            $table->foreign('id_jo_other_item')->references('id_jo_other_item')->on('b06_jo_other_item');
            $table->decimal('amount_lpj', 15, 2)->nullable();
            $table->string('id_md_chart_of_account')->nullable();
            $table->foreign('id_md_chart_of_account')->references('id_md_chart_of_account')->on('a13_md_chart_of_account');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d09_lpj_other_item');
    }
};