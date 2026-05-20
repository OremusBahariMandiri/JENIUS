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
        Schema::create('a04_md_invoice', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_invoice')->unique();
            $table->string('code')->nullable();
            $table->string('invoice_ctg')->nullable();
            $table->string('invoice_typ')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();
            
            $table->index('id_md_invoice');
            $table->index('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a04_md_invoice');
    }
};
