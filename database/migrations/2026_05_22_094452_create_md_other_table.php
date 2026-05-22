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
        Schema::create('a07_md_other', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_other')->unique();
            $table->string('code')->nullable();
            $table->string('other')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('id_md_other');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a07_md_other');
    }
};