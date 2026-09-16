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
        Schema::table('c03_kasbon_tram', function (Blueprint $table) {
            $table->enum('ca_release_status', ['pending', 'release'])->default('pending')->after('due_date');
            $table->date('ca_release_date')->nullable()->after('ca_release_status');
        });
    }

    public function down(): void
    {
        Schema::table('c03_kasbon_tram', function (Blueprint $table) {
            $table->dropColumn(['ca_release_status', 'ca_release_date']);
        });
    }
};
