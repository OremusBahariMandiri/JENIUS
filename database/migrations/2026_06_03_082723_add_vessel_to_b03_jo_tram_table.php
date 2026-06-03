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
    Schema::table('b03_jo_tram', function (Blueprint $table) {
        $table->string('id_md_vessel')->nullable()->after('id_md_port');
        $table->foreign('id_md_vessel')
            ->references('id_md_vessel')
            ->on('a05_md_vessel')
            ->onDelete('set null')
            ->onUpdate('cascade');
        $table->index('id_md_vessel');
    });
}

public function down(): void
{
    Schema::table('b03_jo_tram', function (Blueprint $table) {
        $table->dropForeign(['id_md_vessel']);
        $table->dropIndex(['id_md_vessel']);
        $table->dropColumn('id_md_vessel');
    });
}
};
