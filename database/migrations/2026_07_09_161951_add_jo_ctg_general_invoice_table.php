<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('a04_md_invoice', function (Blueprint $table) {
            DB::statement("ALTER TABLE a04_md_invoice MODIFY COLUMN jo_ctg ENUM('contract','tramper','other','general') NOT NULL");
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
