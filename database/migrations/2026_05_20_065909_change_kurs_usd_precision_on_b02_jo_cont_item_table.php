<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('b02_jo_cont_item', function (Blueprint $table) {
            $table->decimal('kurs_usd', 15, 2)
                  ->nullable()
                  ->default(0)
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('b02_jo_cont_item', function (Blueprint $table) {
            $table->decimal('kurs_usd', 15, 4)
                  ->nullable()
                  ->default(0)
                  ->change();
        });
    }
};