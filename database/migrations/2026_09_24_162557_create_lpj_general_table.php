<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d10_lpj_gen', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_gen', 50)->unique();
            $table->string('no_lpj_gen', 50)->unique();
            $table->date('date');
            $table->decimal('amount', 18, 2)->default(0);
            $table->text('note')->nullable();
            $table->string('evidence')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d10_lpj_gen');
    }
};