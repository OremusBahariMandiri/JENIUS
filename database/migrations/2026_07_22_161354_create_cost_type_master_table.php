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
        Schema::create('a11_md_cost_type', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_cost_type')->unique();
            $table->string('name');
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('id_md_cost_type');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a11_md_cost_type');
    }
};