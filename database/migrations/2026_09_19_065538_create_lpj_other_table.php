<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d07_lpj_other', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_other')->unique();
            $table->string('no_lpj_other')->nullable();
            $table->string('id_jo_other');
            $table->foreign('id_jo_other')->references('id_jo_other')->on('b05_jo_other');
            $table->date('date')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->text('note')->nullable();
            $table->string('evidence')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d07_lpj_other');
    }
};