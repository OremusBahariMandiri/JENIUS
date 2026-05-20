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
        Schema::create('a01_md_customer', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_cust')->unique();
            $table->string('code')->nullable();
            $table->string('customer')->nullable();
            $table->text('address')->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('npwp', 30)->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('id_md_cust');
            $table->index('code');
            $table->index('customer');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a01_md_customer');
    }
};