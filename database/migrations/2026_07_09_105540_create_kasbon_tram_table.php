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
        Schema::create('c03_kasbon_tram', function (Blueprint $table) {
            $table->id();
            $table->string('id_kasbon_tram')->unique();

            // Foreign key to JO Tramper (b03_jo_tram)
            $table->string('id_jo_tram')->nullable();
            $table->foreign('id_jo_tram')
                ->references('id_jo_tram')
                ->on('b03_jo_tram')
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

            // Foreign key to Release (a10_md_release_to)
            $table->string('id_md_release')->nullable();
            $table->foreign('id_md_release')
                ->references('id_md_release')
                ->on('a10_md_release_to')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Cash Advance Number (auto from system)
            $table->integer('nomor')->nullable();

            // Date fields
            $table->date('tgl_kasbon')->nullable();   // Cash Advance Date
            $table->date('tgl_release')->nullable();  // Release Date Request

            // Note
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_kasbon_tram');
            $table->index('id_jo_tram');
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
        Schema::dropIfExists('c03_kasbon_tram');
    }
};