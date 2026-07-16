<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('c06_kasbon_other_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_kasbon_other_item')->unique();

            // Foreign key to Kasbon Other (c05_kasbon_other)
            $table->string('id_kasbon_other')->nullable();
            $table->foreign('id_kasbon_other')
                ->references('id_kasbon_other')
                ->on('c05_kasbon_other')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to JO Other Item (b06_jo_other_item)
            $table->string('id_jo_other_item')->nullable();
            $table->foreign('id_jo_other_item')
                ->references('id_jo_other_item')
                ->on('b06_jo_other_item')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // HPP EPDA value (auto from JO other item HPP)
            $table->decimal('nilai_hpp_other_item', 15, 2)->nullable()->default(0);

            // Advance amount input (IDR)
            $table->decimal('nilai_kasbon', 15, 2)->nullable()->default(0);

            // Total kasbon (auto calculated)
            $table->decimal('total_kasbon', 15, 2)->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_kasbon_other_item');
            $table->index('id_kasbon_other');
            $table->index('id_jo_other_item');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('c06_kasbon_other_item');
    }
};