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
        Schema::table('b02_jo_cont_item', function (Blueprint $table) {
            $table->text('note')->nullable()->after('hargajual_idr');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('b02_jo_cont_item', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};