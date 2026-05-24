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
        Schema::create('b04_jo_tram_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_jo_tram_item')->unique();

            // Foreign key to JO Tramper (b03_jo_tram)
            $table->string('id_jo_tram')->nullable();
            $table->foreign('id_jo_tram')
                ->references('id_jo_tram')
                ->on('b03_jo_tram')
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
            $table->index('id_jo_tram_item');
            $table->index('id_jo_tram');
            $table->index('id_md_invoice');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b04_jo_tram_item');
    }
};