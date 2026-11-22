<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('a15_company_profile', function (Blueprint $table) {
            $table->id('id_company_profile');
            $table->string('nama_perusahaan')->nullable();
            $table->string('nama_direktur')->nullable();
            $table->string('jabatan')->nullable();
            $table->timestamps();
        });

        // Seed satu baris default
        DB::table('a15_company_profile')->insert([
            'nama_perusahaan' => '',
            'nama_direktur'   => '',
            'jabatan'         => '',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('a15_company_profile');
    }
};
