<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('b07_jo_cont_pajak', function (Blueprint $table) {
            $table->id('id_jo_cont_pajak');
            $table->string('id_jo_cont');
            $table->string('id_md_pajak');
            $table->string('nama_pajak');
            $table->decimal('persentase', 5, 2);
            $table->decimal('dasar_pengenaan', 20, 2)->default(0); // selling price yg jadi dasar
            $table->decimal('nominal_pajak', 20, 2)->default(0);   // hasil kalkulasi
            $table->boolean('include_selling')->default(true);  // masuk kolom selling price
            $table->boolean('include_hpp')->default(false);     // masuk kolom HPP
            $table->timestamps();

            $table->foreign('id_jo_cont')->references('id_jo_cont')->on('b01_jo_cont')->onDelete('cascade');
            $table->foreign('id_md_pajak')->references('id_md_pajak')->on('a14_md_pajak')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b07_jo_cont_pajak');
    }
};
