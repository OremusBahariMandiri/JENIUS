<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('f01_invoice_pembayaran_contract', function (Blueprint $table) {
            $table->string('id_md_chart_of_account')->nullable()->after('ba_number');
        });
    }

    public function down(): void
    {
        Schema::table('f01_invoice_pembayaran_contract', function (Blueprint $table) {
            $table->dropColumn('id_md_chart_of_account');
        });
    }
};
