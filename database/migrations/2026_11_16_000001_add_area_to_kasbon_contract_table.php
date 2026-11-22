<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('c01_kasbon_cont', function (Blueprint $table) {
            $table->string('id_md_area')->nullable()->after('id_md_cabang');

            $table->foreign('id_md_area')
                  ->references('id_md_area')
                  ->on('a03_md_area')
                  ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('c01_kasbon_cont', function (Blueprint $table) {
            $table->dropForeign(['id_md_area']);
            $table->dropColumn('id_md_area');
        });
    }
};
