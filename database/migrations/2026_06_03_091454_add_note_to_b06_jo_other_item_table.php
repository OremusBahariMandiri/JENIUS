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
        Schema::table('b06_jo_other_item', function (Blueprint $table) {
            $table->text('note')->nullable()->after('hargajual_idr');
        });
    }

    public function down(): void
    {
        Schema::table('b06_jo_other_item', function (Blueprint $table) {
            $table->dropColumn('note');
        });
    }
};
