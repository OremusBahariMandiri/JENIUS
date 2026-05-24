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
        Schema::create('b03_jo_tram', function (Blueprint $table) {
            $table->id();
            $table->string('id_jo_tram')->unique();

            $table->string('id_md_cust')->nullable();
            $table->foreign('id_md_cust')
                ->references('id_md_cust')
                ->on('a01_md_customer')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to Port (a06_md_port)
            $table->string('id_md_port')->nullable();
            $table->foreign('id_md_port')
                ->references('id_md_port')
                ->on('a06_md_port')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Date fields
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();

            // Text fields
            $table->string('title')->nullable();
            $table->text('note')->nullable();

            // Status proses
            $table->string('sts_proses', 50)->nullable()->default('Draft');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_jo_tram');
            $table->index('id_md_cust');
            $table->index('id_md_port');
            $table->index('sts_proses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b03_jo_tram');
    }
};