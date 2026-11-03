<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('e03_mutasi_pembayaran_detail', function (Blueprint $table) {
            $table->string('id_md_chart_of_account')->nullable()->after('keterangan');
            $table->foreign('id_md_chart_of_account')
                  ->references('id_md_chart_of_account')
                  ->on('a13_md_chart_of_account')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('e03_mutasi_pembayaran_detail', function (Blueprint $table) {
            $table->dropForeign(['id_md_chart_of_account']);
            $table->dropColumn('id_md_chart_of_account');
        });
    }
};
