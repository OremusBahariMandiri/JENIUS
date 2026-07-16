<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('c08_kasbon_gen_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_kasbon_gen_item')->unique();

            // Foreign key to Kasbon General (c07_kasbon_gen)
            $table->string('id_kasbon_gen')->nullable();
            $table->foreign('id_kasbon_gen')
                ->references('id_kasbon_gen')
                ->on('c07_kasbon_gen')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to Invoice (a04_md_invoice) — hanya category general
            $table->string('id_md_invoice')->nullable();
            $table->foreign('id_md_invoice')
                ->references('id_md_invoice')
                ->on('a04_md_invoice')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Nominal kasbon untuk item ini
            $table->decimal('nilai_kasbon', 15, 2)->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_kasbon_gen_item');
            $table->index('id_kasbon_gen');
            $table->index('id_md_invoice');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('c08_kasbon_gen_item');
    }
};