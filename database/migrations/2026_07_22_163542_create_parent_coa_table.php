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
        Schema::create('a12_md_parent_chart_of_account', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_cost_type');
            $table->string('kode_perkiraan', 50)->unique();
            $table->string('nama', 255);
            $table->timestamps();

            $table->foreign('id_md_cost_type')
                  ->references('id_md_cost_type')
                  ->on('a11_md_cost_type')
                  ->onDelete('restrict');

            $table->index('id_md_cost_type');
            $table->index('kode_perkiraan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a12_md_parent_chart_of_account');
    }
};