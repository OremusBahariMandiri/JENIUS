<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Menu     : MASTER DATA - Cabang
     * Database : DbJenius
     * Table    : A09MdBranch
     */
    public function up(): void
    {
        Schema::create('a09_md_branch', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_branch')->unique()->nullable();
            $table->string('skt_branch')->nullable();
            $table->string('nama_branch')->nullable();
            $table->string('area')->nullable();
            $table->string('alamat')->nullable();
            $table->string('no_telepon')->nullable();
            $table->string('email')->nullable();
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
        Schema::dropIfExists('a09_md_branch');
    }
};