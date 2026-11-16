<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('c09_kasbon_gen_pajak', function (Blueprint $table) {
            $table->string('id_kasbon_gen_pajak')->primary();
            $table->string('id_kasbon_gen');
            $table->string('id_md_pajak')->nullable();
            $table->string('nama_pajak');
            $table->decimal('persentase', 10, 2)->default(0);
            $table->decimal('dasar_pengenaan', 20, 2)->default(0);
            $table->decimal('nominal_pajak', 20, 2)->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_kasbon_gen')
                  ->references('id_kasbon_gen')
                  ->on('c07_kasbon_gen')
                  ->onDelete('cascade');

            $table->foreign('id_md_pajak')
                  ->references('id_md_pajak')
                  ->on('a14_md_pajak')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('c09_kasbon_gen_pajak');
    }
};
