<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('e04_mutasi_penerimaan', function (Blueprint $table) {
            $table->id();

            $table->string('nomor', 50)->unique()->comment('Nomor dokumen penerimaan');
            $table->string('no_cek', 100)->nullable()->comment('No. cek / giro / referensi');
            $table->date('tanggal');
            $table->decimal('kurs', 18, 4)->default(1);
            $table->text('memo')->nullable();
            $table->string('created_by', 100)->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('e04_mutasi_penerimaan');
    }
};