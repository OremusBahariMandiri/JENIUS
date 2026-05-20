<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('a03_md_area', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_area')->unique();
            $table->string('code')->nullable();
            $table->string('area')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('id_md_area');
            $table->index('code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('a03_md_area');
    }
};