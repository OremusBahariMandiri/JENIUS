<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ubah enum jenis agar include 'custom'
        Schema::table('e03_mutasi_pembayaran_detail', function (Blueprint $table) {
            // MySQL: change enum via raw statement
        });
        DB::statement("ALTER TABLE e03_mutasi_pembayaran_detail MODIFY jenis ENUM('tramper','other','contract','general','custom') NOT NULL");

        // 2. Tambah kolom custom fields
        Schema::table('e03_mutasi_pembayaran_detail', function (Blueprint $table) {
            $table->string('custom_id_md_release')->nullable()->after('id_md_chart_of_account');
            $table->string('custom_id_md_dep')->nullable()->after('custom_id_md_release');
            $table->string('custom_id_md_branch')->nullable()->after('custom_id_md_dep');
            $table->decimal('custom_nilai', 15, 2)->nullable()->after('custom_id_md_branch');
            $table->decimal('custom_pj', 15, 2)->nullable()->after('custom_nilai');

            $table->foreign('custom_id_md_release')->references('id_md_release')->on('a10_md_release_to')->nullOnDelete();
            $table->foreign('custom_id_md_dep')->references('id_md_dep')->on('a08_md_dep')->nullOnDelete();
            $table->foreign('custom_id_md_branch')->references('id_md_branch')->on('a09_md_branch')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('e03_mutasi_pembayaran_detail', function (Blueprint $table) {
            $table->dropForeign(['custom_id_md_release']);
            $table->dropForeign(['custom_id_md_dep']);
            $table->dropForeign(['custom_id_md_branch']);
            $table->dropColumn(['custom_id_md_release', 'custom_id_md_dep', 'custom_id_md_branch', 'custom_nilai', 'custom_pj']);
        });
        DB::statement("ALTER TABLE e03_mutasi_pembayaran_detail MODIFY jenis ENUM('tramper','other','contract','general') NOT NULL");
    }
};
