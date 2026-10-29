<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('c03_kasbon_cont_pajak', function (Blueprint $table) {
            $table->id();
            $table->string('id_kasbon_cont_pajak')->unique();

            // Foreign key to Kasbon Contract (c01_kasbon_cont)
            $table->string('id_kasbon_cont')->nullable();
            $table->foreign('id_kasbon_cont')
                ->references('id_kasbon_cont')
                ->on('c01_kasbon_cont')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to JO Contract Pajak (b07_jo_cont_pajak)
            $table->unsignedBigInteger('id_jo_cont_pajak')->nullable();
            $table->foreign('id_jo_cont_pajak')
                ->references('id_jo_cont_pajak')
                ->on('b07_jo_cont_pajak')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Snapshot dari JO pajak
            $table->string('nama_pajak')->nullable();
            $table->decimal('persentase', 8, 2)->nullable()->default(0);
            $table->decimal('nominal_pajak', 15, 2)->nullable()->default(0); // nominal di JO
            $table->boolean('include_selling')->default(true);
            $table->boolean('include_hpp')->default(false);

            // CA amount untuk pajak ini
            $table->decimal('nilai_kasbon_pajak', 15, 2)->nullable()->default(0);

            $table->timestamps();
            $table->softDeletes();

            $table->index('id_kasbon_cont_pajak');
            $table->index('id_kasbon_cont');
            $table->index('id_jo_cont_pajak');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('c03_kasbon_cont_pajak');
    }
};
