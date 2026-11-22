<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('c06_kasbon_other_item', function (Blueprint $table) {
            $table->string('item_type', 10)->default('item')->after('id_kasbon_other_item');
            $table->string('id_jo_other_pajak', 50)->nullable()->after('id_jo_other_item');
        });
    }

    public function down(): void
    {
        Schema::table('c06_kasbon_other_item', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'id_jo_other_pajak']);
        });
    }
};
