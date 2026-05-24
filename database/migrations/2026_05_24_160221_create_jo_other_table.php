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
        Schema::create('b05_jo_other', function (Blueprint $table) {
            $table->id();
            $table->string('id_jo_other')->unique();

            // Foreign key to Customer (a01_md_customer)
            $table->string('id_md_cust')->nullable();
            $table->foreign('id_md_cust')
                ->references('id_md_cust')
                ->on('a01_md_customer')
                ->onDelete('set null')
                ->onUpdate('cascade');

            // Foreign key to Other Type (a07_md_other)
            $table->string('id_md_other')->nullable();
            $table->foreign('id_md_other')
                ->references('id_md_other')
                ->on('a07_md_other')
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
            $table->index('id_jo_other');
            $table->index('id_md_cust');
            $table->index('id_md_other');
            $table->index('id_md_port');
            $table->index('sts_proses');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('b05_jo_other');
    }
};