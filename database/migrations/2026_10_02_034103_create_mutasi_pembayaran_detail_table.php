<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e03_mutasi_pembayaran_detail', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_mutasi_voucher');
            $table->enum('jenis', ['tramper', 'other', 'contract', 'general']);

            // JO references (nullable, hanya 1 terisi sesuai jenis, general = null semua)
            $table->string('id_jo_tram')->nullable();
            $table->string('id_jo_other')->nullable();
            $table->string('id_jo_cont')->nullable();

            // Kasbon references (nullable, hanya 1 terisi sesuai jenis)
            $table->unsignedBigInteger('id_kasbon_tram')->nullable();
            $table->unsignedBigInteger('id_kasbon_other')->nullable();
            $table->unsignedBigInteger('id_kasbon_cont')->nullable();
            $table->unsignedBigInteger('id_kasbon_gen')->nullable();

            // Nilai finansial (disimpan snapshot agar tidak berubah jika LPJ diupdate)
            $table->decimal('nilai', 15, 2)->default(0);
            $table->decimal('pj', 15, 2)->default(0);
            $table->decimal('selisih', 15, 2)->default(0);

            $table->string('keterangan')->nullable();
            $table->timestamps();

            // ── Foreign Keys ──
            $table->foreign('id_mutasi_voucher')
                  ->references('id')
                  ->on('e02_mutasi_pembayaran_voucher')
                  ->cascadeOnDelete();

            $table->foreign('id_jo_tram')
                  ->references('id_jo_tram')
                  ->on('b03_jo_tram')
                  ->restrictOnDelete();

            $table->foreign('id_jo_other')
                  ->references('id_jo_other')
                  ->on('b05_jo_other')
                  ->restrictOnDelete();

            $table->foreign('id_jo_cont')
                  ->references('id_jo_cont')
                  ->on('b01_jo_cont')
                  ->restrictOnDelete();

            $table->foreign('id_kasbon_tram')
                  ->references('id')
                  ->on('c03_kasbon_tram')
                  ->restrictOnDelete();

            $table->foreign('id_kasbon_other')
                  ->references('id')
                  ->on('c05_kasbon_other')
                  ->restrictOnDelete();

            $table->foreign('id_kasbon_cont')
                  ->references('id')
                  ->on('c01_kasbon_cont')
                  ->restrictOnDelete();

            $table->foreign('id_kasbon_gen')
                  ->references('id')
                  ->on('c07_kasbon_gen')
                  ->restrictOnDelete();

            // ── Constraint: 1 kasbon tidak boleh muncul 2x dalam 1 voucher ──
            $table->unique(['id_mutasi_voucher', 'id_kasbon_tram'],  'uniq_voucher_kasbon_tram');
            $table->unique(['id_mutasi_voucher', 'id_kasbon_other'], 'uniq_voucher_kasbon_other');
            $table->unique(['id_mutasi_voucher', 'id_kasbon_cont'],  'uniq_voucher_kasbon_cont');
            $table->unique(['id_mutasi_voucher', 'id_kasbon_gen'],   'uniq_voucher_kasbon_gen');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e03_mutasi_pembayaran_detail');
    }
};