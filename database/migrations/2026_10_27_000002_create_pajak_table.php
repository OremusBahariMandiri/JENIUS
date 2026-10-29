<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('a14_md_pajak', function (Blueprint $table) {
            $table->string('id_md_pajak')->primary();
            $table->string('nama_pajak');
            $table->decimal('persentase', 5, 2); // e.g. 11.00 for 11%
            $table->string('kode_pajak')->nullable(); // e.g. PPN, PPh21
            $table->text('keterangan')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('a14_md_pajak');
    }
};
