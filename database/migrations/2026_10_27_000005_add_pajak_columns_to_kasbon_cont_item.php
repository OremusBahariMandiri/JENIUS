<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('c02_kasbon_cont_item', function (Blueprint $table) {
            // Nullable FK ke b07_jo_cont_pajak; diisi jika row ini adalah pajak
            $table->unsignedBigInteger('id_jo_cont_pajak')->nullable()->after('id_jo_cont_item');
            // 'item' = baris biasa, 'tax' = baris pajak
            $table->string('item_type', 10)->default('item')->after('id_jo_cont_pajak');

            $table->index('id_jo_cont_pajak');
        });
    }

    public function down(): void
    {
        Schema::table('c02_kasbon_cont_item', function (Blueprint $table) {
            $table->dropIndex(['id_jo_cont_pajak']);
            $table->dropColumn(['id_jo_cont_pajak', 'item_type']);
        });
    }
};
