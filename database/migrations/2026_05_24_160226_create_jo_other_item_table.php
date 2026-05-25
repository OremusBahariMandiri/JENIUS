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
        Schema::create('b06_jo_other_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_jo_other_item')->unique();

            // Foreign key to JO Other (b05_jo_other)
            $table->string('id_jo_other')->nullable();
            $table->foreign('id_jo_other')
                ->references('id_jo_other')
                ->on('b05_jo_other')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to Invoice Category (a04_md_invoice)
            $table->string('id_md_invoice')->nullable();
            $table->foreign('id_md_invoice')
                ->references('id_md_invoice')
                ->on('a04_md_invoice')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Revenue fields (Pendapatan)
            $table->decimal('pendapatan_idr', 15, 2)->nullable()->default(0);
            $table->decimal('pendapatan_usd', 15, 2)->nullable()->default(0);

            // Exchange rate (Kurs)
            $table->decimal('kurs_usd', 15, 4)->nullable()->default(0);
            $table->dateTime('tgl_kurs_usd')->nullable();

            // Operational cost (HPP)
            $table->decimal('hpp_ops', 15, 2)->nullable()->default(0);

            // Selling price (Harga Jual)
            $table->decimal('hargajual_idr', 15, 2)->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_jo_other_item');
            $table->index('id_jo_other');
            $table->index('id_md_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b06_jo_other_item');
    }
};