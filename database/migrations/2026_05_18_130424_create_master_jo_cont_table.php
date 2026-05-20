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
        Schema::create('b01_jo_cont', function (Blueprint $table) {
            $table->id();
            $table->string('id_jo_cont')->unique();

            // This one is now correct (string)
            $table->string('id_md_cont')->nullable();
            $table->foreign('id_md_cont')
                ->references('id_md_cont')
                ->on('a02_md_contract')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Change this from unsignedBigInteger to string
            $table->string('id_md_area')->nullable();
            $table->foreign('id_md_area')
                ->references('id_md_area')
                ->on('a03_md_area')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->string('title')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            $table->index('id_jo_cont');
            $table->index('id_md_cont');
            $table->index('id_md_area');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b01_jo_cont');
    }
};