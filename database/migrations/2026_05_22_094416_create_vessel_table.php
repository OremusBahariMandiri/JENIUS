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
        Schema::create('a05_md_vessel', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_vessel')->unique();
            $table->string('vessel_name')->nullable();
            $table->string('vessel_type')->nullable();
            $table->string('no_imo')->nullable();
            $table->string('no_mmsi')->nullable();
            $table->string('call_sign')->nullable();
            $table->string('gt')->nullable();
            $table->string('dwt')->nullable();
            $table->string('year_built')->nullable();
            $table->string('breadth')->nullable();
            $table->string('loa')->nullable();
            $table->string('flag')->nullable();
            $table->string('ga')->nullable();
            $table->text('ship_particular')->nullable();
            $table->timestamps();

            $table->index('id_md_vessel');
            $table->index('vessel_name');
            $table->index('no_imo');
            $table->index('call_sign');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a05_md_vessel');
    }
};