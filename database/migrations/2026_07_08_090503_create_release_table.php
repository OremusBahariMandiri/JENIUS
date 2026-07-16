<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menu     : MASTER DATA - RELEASE To
     * Database : DbJenius
     * Table    : A10MdReleaseTo
     */
    public function up(): void
    {
        Schema::create('a10_md_release_to', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_release')->unique()->nullable();
            $table->string('nama_release')->nullable();
            $table->string('tujuan')->nullable();
            $table->string('rekening')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('alamat')->nullable();
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a10_md_release_to');
    }
};