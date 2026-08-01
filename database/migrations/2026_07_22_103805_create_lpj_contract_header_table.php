<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d01_lpj_cont', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_cont')->unique();
            $table->string('no_lpj_cont')->nullable();
            $table->string('id_jo_cont');
            $table->foreign('id_jo_cont')->references('id_jo_cont')->on('b01_jo_cont');
            $table->date('date');
            $table->decimal('amount', 18, 2)->default(0);
            $table->text('note')->nullable();
            $table->string('evidence')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d01_lpj_cont');
    }
};