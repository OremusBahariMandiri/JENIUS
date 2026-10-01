<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('d12_lpj_gen_item', function (Blueprint $table) {
            $table->id();
            $table->string('id_lpj_gen_item', 50)->unique();
            $table->string('id_lpj_gen', 50);
            $table->string('id_kasbon_gen_item', 50);   // FK ke c08_kasbon_gen_item
            $table->decimal('amount_lpj', 18, 2)->default(0);
            $table->string('id_md_chart_of_account')->nullable(); // FK ke COA
            // origin: jika item ditambah manual dari halaman LPJ (bukan dari kasbon existing)
            $table->string('origin_lpj_gen', 50)->nullable();
            $table->timestamps();

            $table->foreign('id_lpj_gen')
                ->references('id_lpj_gen')
                ->on('d10_lpj_gen')
                ->onDelete('cascade');

            $table->foreign('id_kasbon_gen_item')
                ->references('id_kasbon_gen_item')
                ->on('c08_kasbon_gen_item')
                ->onDelete('restrict');

            $table->foreign('id_md_chart_of_account')
                ->references('id_md_chart_of_account')
                ->on('a13_md_chart_of_account')
                ->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('d12_lpj_gen_item');
    }
};