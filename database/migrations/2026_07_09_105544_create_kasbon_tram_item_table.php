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
        Schema::create('c04_kasbon_tram_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_kasbon_tram_item')->unique();

            // Foreign key to Kasbon Tramper (c03_kasbon_tram)
            $table->string('id_kasbon_tram')->nullable();
            $table->foreign('id_kasbon_tram')
                ->references('id_kasbon_tram')
                ->on('c03_kasbon_tram')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to JO Tramper Item (b04_jo_tram_item)
            $table->string('id_jo_tram_item')->nullable();
            $table->foreign('id_jo_tram_item')
                ->references('id_jo_tram_item')
                ->on('b04_jo_tram_item')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // HPP EPDA value (auto from JO tramper item HPP)
            $table->decimal('nilai_hpp_tram_item', 15, 2)->nullable()->default(0);

            // Advance amount input (IDR)
            $table->decimal('nilai_kasbon', 15, 2)->nullable()->default(0);

            // Total kasbon (auto calculated)
            $table->decimal('total_kasbon', 15, 2)->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_kasbon_tram_item');
            $table->index('id_kasbon_tram');
            $table->index('id_jo_tram_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c04_kasbon_tram_item');
    }
};