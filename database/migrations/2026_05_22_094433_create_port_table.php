<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('a06_md_port', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_port')->unique();
            $table->string('no_port')->nullable();
            $table->string('name_port')->nullable();
            $table->string('alamat')->nullable();
            $table->string('kota')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('negara')->nullable();
            $table->string('port_type')->nullable();
            $table->string('operator_port')->nullable();
            $table->string('draft')->nullable();
            $table->string('panjang_kapal')->nullable();
            $table->string('lebar_kapal')->nullable();
            $table->string('dwt')->nullable();
            $table->string('panjang_dermaga')->nullable();
            $table->string('pasang_surut')->nullable();
            $table->string('jam_ops')->nullable();
            $table->timestamps();

            $table->index('id_md_port');
            $table->index('no_port');
            $table->index('name_port');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a06_md_port');
    }
};