<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e02_mutasi_pembayaran_voucher', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mutasi_pembayaran');
            $table->string('nomor_voucher')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedTinyInteger('urutan')->default(1);
            $table->timestamps();

            $table->foreign('id_mutasi_pembayaran')
                  ->references('id')
                  ->on('e01_mutasi_pembayaran')
                  ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e02_mutasi_pembayaran_voucher');
    }
};