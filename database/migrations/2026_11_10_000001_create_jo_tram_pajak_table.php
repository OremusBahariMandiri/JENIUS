<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('b08_jo_tram_pajak', function (Blueprint $table) {
            $table->id('id_jo_tram_pajak');
            $table->string('id_jo_tram');
            $table->string('id_md_pajak');
            $table->string('nama_pajak');
            $table->decimal('persentase', 5, 2);
            $table->decimal('dasar_pengenaan', 20, 2)->default(0);
            $table->decimal('nominal_pajak', 20, 2)->default(0);
            $table->boolean('include_selling')->default(true);
            $table->boolean('include_hpp')->default(false);
            $table->timestamps();

            $table->foreign('id_jo_tram')->references('id_jo_tram')->on('b03_jo_tram')->onDelete('cascade');
            $table->foreign('id_md_pajak')->references('id_md_pajak')->on('a14_md_pajak')->onDelete('restrict');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('b08_jo_tram_pajak');
    }
};
