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
        Schema::create('c01_kasbon_cont', function (Blueprint $table) {
            $table->id();
            $table->string('id_kasbon_cont')->unique();

            // Foreign key to JO Contract (b01_jo_cont)
            $table->string('id_jo_cont')->nullable();
            $table->foreign('id_jo_cont')
                ->references('id_jo_cont')
                ->on('b01_jo_cont')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to Departemen (a08_md_dep)
            $table->string('id_md_dep')->nullable();
            $table->foreign('id_md_dep')
                ->references('id_md_dep')
                ->on('a08_md_dep')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to Branch (a09_md_branch)
            $table->string('id_md_cabang')->nullable();
            $table->foreign('id_md_cabang')
                ->references('id_md_branch')
                ->on('a09_md_branch')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to Release (a10_md_release)
            $table->string('id_md_release')->nullable();
            $table->foreign('id_md_release')
                ->references('id_md_release')
                ->on('a10_md_release_to')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Cash advance number (auto from system)
            $table->integer('nomor')->nullable();

            // Date fields
            $table->date('tgl_kasbon')->nullable();
            $table->date('tgl_release')->nullable();

            // Note
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_kasbon_cont');
            $table->index('id_jo_cont');
            $table->index('id_md_dep');
            $table->index('id_md_cabang');
            $table->index('id_md_release');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c01_kasbon_cont');
    }
};