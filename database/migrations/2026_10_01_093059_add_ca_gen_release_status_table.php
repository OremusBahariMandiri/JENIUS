<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('c07_kasbon_gen', function (Blueprint $table) {
            $table->string('ca_release_status')->nullable()->after('due_date');
            $table->date('ca_release_date')->nullable()->after('ca_release_status');
        });
    }

    public function down(): void
    {
        Schema::table('c07_kasbon_gen', function (Blueprint $table) {
            $table->dropColumn(['ca_release_status', 'ca_release_date']);
        });
    }
};