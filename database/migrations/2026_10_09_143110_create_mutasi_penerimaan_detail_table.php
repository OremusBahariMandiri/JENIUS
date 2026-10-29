<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e06_mutasi_penerimaan_detail', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_mutasi_voucher')
                ->comment('FK → e05_mutasi_penerimaan_voucher');

            $table->enum('jenis', ['tramper', 'other', 'contract', 'general']);

            $table->enum('tipe_pembayaran', [
                'uang_muka_pembayaran',
                'penerimaan_pembayaran',
                'pengembalian_lebih_bayar',
            ]);

            // JO references
            $table->string('id_jo_tram',  50)->nullable();
            $table->string('id_jo_other', 50)->nullable();
            $table->string('id_jo_cont',  50)->nullable();

            // Kasbon references (untuk pengembalian)
            $table->unsignedBigInteger('id_kasbon_tram')->nullable();
            $table->unsignedBigInteger('id_kasbon_other')->nullable();
            $table->unsignedBigInteger('id_kasbon_cont')->nullable();
            $table->unsignedBigInteger('id_kasbon_gen')->nullable();

            // Data turunan
            $table->string('id_md_cust',   50)->nullable()->comment('Auto dari JO');
            $table->string('id_md_dep',    50)->nullable()->comment('Manual (select)');
            $table->string('id_md_branch', 50)->nullable()->comment('Manual (select)');

            // Nilai — COA tidak ada di detail, pakai COA voucher
            $table->decimal('nilai', 18, 2)->default(0);
            $table->text('keterangan')->nullable();

            $table->timestamps();

            // Foreign keys
            $table->foreign('id_mutasi_voucher')
                ->references('id')->on('e05_mutasi_penerimaan_voucher')
                ->cascadeOnDelete();

            $table->foreign('id_md_cust')
                ->references('id_md_cust')->on('a01_md_customer')->nullOnDelete();

            $table->foreign('id_md_dep')
                ->references('id_md_dep')->on('a08_md_dep')->nullOnDelete();

            $table->foreign('id_md_branch')
                ->references('id_md_branch')->on('a09_md_branch')->nullOnDelete();

            $table->index(['id_kasbon_tram', 'id_kasbon_other', 'id_kasbon_cont', 'id_kasbon_gen'], 'e06_kasbon_refs_index');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e06_mutasi_penerimaan_detail');
    }
};