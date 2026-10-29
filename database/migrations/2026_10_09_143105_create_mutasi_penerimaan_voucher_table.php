<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e05_mutasi_penerimaan_voucher', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('id_mutasi_penerimaan')
                ->comment('FK → e04_mutasi_penerimaan');

            $table->string('nomor_voucher', 100)->nullable();
            $table->text('keterangan')->nullable();
            $table->date('tanggal')->nullable()->comment('Tanggal dana masuk');

            $table->string('id_md_chart_of_account', 50)
                ->nullable()
                ->comment('FK → a13_md_chart_of_account (COA kas/bank penerima)');

            $table->unsignedSmallInteger('urutan')->default(0);

            $table->timestamps();

            $table->foreign('id_mutasi_penerimaan')
                ->references('id')
                ->on('e04_mutasi_penerimaan')
                ->cascadeOnDelete();

            $table->foreign('id_md_chart_of_account')
                ->references('id_md_chart_of_account')
                ->on('a13_md_chart_of_account')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e05_mutasi_penerimaan_voucher');
    }
};