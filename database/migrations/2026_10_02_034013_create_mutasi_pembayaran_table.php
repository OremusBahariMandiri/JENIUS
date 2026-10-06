<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e01_mutasi_pembayaran', function (Blueprint $table) {
            $table->id();
            $table->string('nomor')->unique();
            $table->string('id_md_chart_of_account'); // FK ke COA kas/bank (a13)
            $table->string('no_cek')->nullable();
            $table->date('tanggal');
            $table->decimal('kurs', 15, 4)->default(1);
            $table->text('memo')->nullable();
            $table->string('created_by')->nullable();
            $table->timestamps();

            $table->foreign('id_md_chart_of_account')
                  ->references('id_md_chart_of_account')
                  ->on('a13_md_chart_of_account')
                  ->restrictOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e01_mutasi_pembayaran');
    }
};