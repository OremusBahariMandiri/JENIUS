<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('a13_md_chart_of_account', function (Blueprint $table) {
            $table->string('invoice_display', 255)->nullable()->after('payment_type');
            $table->string('coa_code', 100)->nullable()->after('invoice_display');
            $table->string('number', 100)->nullable()->after('coa_code');
        });
    }

    public function down(): void
    {
        Schema::table('a13_md_chart_of_account', function (Blueprint $table) {
            $table->dropColumn(['invoice_display', 'coa_code', 'number']);
        });
    }
};
