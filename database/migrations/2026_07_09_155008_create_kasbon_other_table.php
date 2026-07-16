<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('c05_kasbon_other', function (Blueprint $table) {
            $table->id();
            $table->string('id_kasbon_other')->unique();

            // Foreign key to JO Other (b05_jo_other)
            $table->string('id_jo_other')->nullable();
            $table->foreign('id_jo_other')
                ->references('id_jo_other')
                ->on('b05_jo_other')
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
            $table->date('tgl_kasbon')->nullable();
            $table->date('tgl_release')->nullable();

            // Note
            $table->text('note')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_kasbon_other');
            $table->index('id_jo_other');
            $table->index('id_md_dep');
            $table->index('id_md_cabang');
            $table->index('id_md_release');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('c05_kasbon_other');
    }
};