<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only add columns if the table exists but columns don't
        if (Schema::hasTable('b07_jo_cont_pajak') && !Schema::hasColumn('b07_jo_cont_pajak', 'include_selling')) {
            Schema::table('b07_jo_cont_pajak', function (Blueprint $table) {
                $table->boolean('include_selling')->default(true)->after('nominal_pajak');
                $table->boolean('include_hpp')->default(false)->after('include_selling');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('b07_jo_cont_pajak')) {
            Schema::table('b07_jo_cont_pajak', function (Blueprint $table) {
                $table->dropColumn(['include_selling', 'include_hpp']);
            });
        }
    }
};
