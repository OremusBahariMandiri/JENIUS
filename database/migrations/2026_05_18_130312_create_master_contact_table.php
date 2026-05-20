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
        Schema::create('a02_md_contract', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_cont')->unique();
            $table->string('no_contract')->nullable();
            $table->string('contract')->nullable();

            // Change this to match the parent table's data type
            $table->string('id_md_cust')->nullable();
            $table->foreign('id_md_cust')
                ->references('id_md_cust')
                ->on('a01_md_customer')
                ->onDelete('set null')
                ->onUpdate('cascade');

            $table->decimal('expenditure', 15, 2)->nullable()->default(0);
            $table->date('date_start')->nullable();
            $table->date('date_end')->nullable();
            $table->text('note')->nullable();
            $table->timestamps();

            // Indexes
            $table->index('id_md_cont');
            $table->index('no_contract');
            $table->index('id_md_cust');
            $table->index('date_start');
            $table->index('date_end');
        });
    }
};
