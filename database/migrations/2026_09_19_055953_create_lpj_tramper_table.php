<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d04_lpj_tram', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_tram')->unique();
            $table->string('id_jo_tram');
            $table->foreign('id_jo_tram')->references('id_jo_tram')->on('b03_jo_tram');
            $table->string('no_lpj_tram')->nullable();
            $table->date('date')->nullable();
            $table->decimal('amount', 15, 2)->nullable();
            $table->text('note')->nullable();
            $table->string('evidence')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d04_lpj_tram');
    }
};