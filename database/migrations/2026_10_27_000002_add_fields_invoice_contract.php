<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add close/reopen notes to header
        Schema::table('f01_invoice_pembayaran_contract', function (Blueprint $table) {
            $table->text('close_note')->nullable()->after('status');
            $table->text('reopen_note')->nullable()->after('close_note');
        });

        // Add item_type, id_jo_cont_pajak, deskripsi to items
        Schema::table('f02_invoice_pembayaran_contract_item', function (Blueprint $table) {
            $table->string('item_type')->default('item')->after('id_jo_cont_item'); // 'item' or 'tax'
            $table->unsignedBigInteger('id_jo_cont_pajak')->nullable()->after('item_type');
            $table->text('deskripsi')->nullable()->after('id_md_chart_of_account');
        });
    }

    public function down(): void
    {
        Schema::table('f01_invoice_pembayaran_contract', function (Blueprint $table) {
            $table->dropColumn(['close_note', 'reopen_note']);
        });
        Schema::table('f02_invoice_pembayaran_contract_item', function (Blueprint $table) {
            $table->dropColumn(['item_type', 'id_jo_cont_pajak', 'deskripsi']);
        });
    }
};
