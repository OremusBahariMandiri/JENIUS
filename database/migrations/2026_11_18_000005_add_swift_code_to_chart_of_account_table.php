<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('a13_md_chart_of_account', function (Blueprint $table) {
            $table->string('swift_code')->nullable()->after('number');
        });
    }

    public function down(): void
    {
        Schema::table('a13_md_chart_of_account', function (Blueprint $table) {
            $table->dropColumn('swift_code');
        });
    }
};
