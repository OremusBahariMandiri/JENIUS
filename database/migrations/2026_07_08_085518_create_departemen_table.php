<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('a08_md_dep', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_dep')->unique();
            $table->string('skt_dep')->nullable();
            $table->string('nama_dep')->nullable();
            $table->string('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a08_md_dep');
    }
};
