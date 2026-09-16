<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ── Tambah kolom ke c05_kasbon_other ────────────────────────────
        Schema::table('c05_kasbon_other', function (Blueprint $table) {
            $table->string('ca_release_status', 20)
                  ->default('pending')
                  ->after('due_date')
                  ->comment('pending | release');

            $table->date('ca_release_date')
                  ->nullable()
                  ->after('ca_release_status');
        });

        // ── Tambah kolom ke c06_kasbon_other_item ───────────────────────
        Schema::table('c06_kasbon_other_item', function (Blueprint $table) {
            $table->string('origin_lpj_other', 50)
                  ->nullable()
                  ->after('total_kasbon')
                  ->comment('filled when item was created from an LPJ entry');
        });
    }

    public function down(): void
    {
        Schema::table('c05_kasbon_other', function (Blueprint $table) {
            $table->dropColumn(['ca_release_status', 'ca_release_date']);
        });

        Schema::table('c06_kasbon_other_item', function (Blueprint $table) {
            $table->dropColumn('origin_lpj_other');
        });
    }
};