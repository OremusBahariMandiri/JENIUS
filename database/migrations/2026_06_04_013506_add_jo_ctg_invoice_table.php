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
        Schema::table('a04_md_invoice', function (Blueprint $table) {
            $table->enum('jo_ctg', ['contract', 'tramper', 'other'])
                  ->nullable()
                  ->after('invoice_typ')
                  ->comment('Job Order Category: contract, tramper, other');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('a04_md_invoice', function (Blueprint $table) {
            $table->dropColumn('jo_ctg');
        });
    }
};