<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('e06_mutasi_penerimaan_detail', function (Blueprint $table) {
            $table->string('id_md_chart_of_account', 50)->nullable()->after('id_md_branch');
        });
    }

    public function down(): void
    {
        Schema::table('e06_mutasi_penerimaan_detail', function (Blueprint $table) {
            $table->dropColumn('id_md_chart_of_account');
        });
    }
};
