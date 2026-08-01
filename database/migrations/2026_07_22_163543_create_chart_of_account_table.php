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
        Schema::create('a13_md_chart_of_account', function (Blueprint $table) {
            $table->id();
            $table->string('id_md_chart_of_account')->unique();
            $table->string('parrent')->nullable()->comment('FK ke a12_md_parent_chart_of_account.kode_perkiraan');
            $table->string('no_account')->nullable();
            $table->string('account_name');
            $table->string('type')->nullable();
            $table->string('payment_type')->nullable();
            $table->decimal('opening_balance', 20, 2)->nullable()->default(0);
            $table->decimal('current_balance', 20, 2)->nullable()->default(0);
            $table->timestamps();

            $table->index('id_md_chart_of_account');
            $table->index('parrent');
            $table->index('no_account');
            $table->index('account_name');

            $table->foreign('parrent')
                  ->references('kode_perkiraan')
                  ->on('a12_md_parent_chart_of_account')
                  ->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('a13_md_chart_of_account');
    }
};