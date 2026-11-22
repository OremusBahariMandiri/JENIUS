<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // jo-contract pajak
        Schema::table('b07_jo_cont_pajak', function (Blueprint $table) {
            $table->decimal('dasar_pengenaan_hpp', 20, 2)->default(0)->after('dasar_pengenaan');
            $table->decimal('nominal_pajak_hpp', 20, 2)->default(0)->after('nominal_pajak');
        });

        // jo-tramper pajak
        Schema::table('b08_jo_tram_pajak', function (Blueprint $table) {
            $table->decimal('dasar_pengenaan_hpp', 20, 2)->default(0)->after('dasar_pengenaan');
            $table->decimal('nominal_pajak_hpp', 20, 2)->default(0)->after('nominal_pajak');
        });
    }

    public function down(): void
    {
        Schema::table('b07_jo_cont_pajak', function (Blueprint $table) {
            $table->dropColumn(['dasar_pengenaan_hpp', 'nominal_pajak_hpp']);
        });
        Schema::table('b08_jo_tram_pajak', function (Blueprint $table) {
            $table->dropColumn(['dasar_pengenaan_hpp', 'nominal_pajak_hpp']);
        });
    }
};
