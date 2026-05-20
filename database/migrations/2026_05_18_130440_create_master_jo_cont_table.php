<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('b02_jo_cont_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_jo_cont_item')->unique();

            // Change from unsignedBigInteger to string
            $table->string('id_jo_cont')->nullable();
            $table->foreign('id_jo_cont')
                  ->references('id_jo_cont')
                  ->on('b01_jo_cont')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            // Change from unsignedBigInteger to string (check a04_md_invoice table)
            $table->string('id_md_invoice')->nullable();
            $table->foreign('id_md_invoice')
                  ->references('id_md_invoice')
                  ->on('a04_md_invoice')
                  ->onDelete('set null')
                  ->onUpdate('cascade');

            $table->decimal('pendapatan_idr', 15, 2)->nullable()->default(0);
            $table->decimal('pendapatan_usd', 15, 2)->nullable()->default(0);
            $table->decimal('kurs_usd', 15, 4)->nullable()->default(0);
            $table->dateTime('tgl_kurs_usd')->nullable();
            $table->decimal('hpp_ops', 15, 2)->nullable()->default(0);
            $table->decimal('hargajual_idr', 15, 2)->nullable()->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->index('id_jo_cont_item');
            $table->index('id_jo_cont');
            $table->index('id_md_invoice');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b02_jo_cont_item');
    }
};