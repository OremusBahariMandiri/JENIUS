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
        Schema::create('c02_kasbon_cont_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_kasbon_cont_item')->unique();

            // Foreign key to Kasbon Contract (c01_kasbon_cont)
            $table->string('id_kasbon_cont')->nullable();
            $table->foreign('id_kasbon_cont')
                ->references('id_kasbon_cont')
                ->on('c01_kasbon_cont')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to JO Contract Item (b02_jo_cont_item)
            $table->string('id_jo_cont_item')->nullable();
            $table->foreign('id_jo_cont_item')
                ->references('id_jo_cont_item')
                ->on('b02_jo_cont_item')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // HPP EPDA value (auto from JO contract item HPP)
            $table->decimal('nilai_hpp_cont_item', 15, 2)->nullable()->default(0);

            // Advance amount input
            $table->decimal('nilai_kasbon', 15, 2)->nullable()->default(0);

            // Total kasbon (auto calculated)
            $table->decimal('total_kasbon', 15, 2)->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_kasbon_cont_item');
            $table->index('id_kasbon_cont');
            $table->index('id_jo_cont_item');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('c02_kasbon_cont_item');
    }
};