<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('d03_lpj_cont_item', function (Blueprint $table) {
            // Make id_jo_cont_item nullable (tax rows do not have a JO item)
            $table->string('id_jo_cont_item')->nullable()->change();

            // 'item' = regular row, 'tax' = pajak row
            $table->string('item_type', 10)->default('item')->after('id_jo_cont_item');

            // FK to kasbon pajak row (only for tax rows; references c02_kasbon_cont_item which holds tax rows)
            // id_kasbon_cont_item is already present and covers both item & tax rows
        });
    }

    public function down(): void
    {
        Schema::table('d03_lpj_cont_item', function (Blueprint $table) {
            $table->string('id_jo_cont_item')->nullable(false)->change();
            $table->dropColumn('item_type');
        });
    }
};
