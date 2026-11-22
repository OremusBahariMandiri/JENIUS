<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('f03_invoice_pembayaran_tramper', function (Blueprint $table) {
            $table->id('id_invoice_tramper');
            $table->string('no_invoice')->unique();
            $table->string('id_jo_tram')->nullable();
            $table->string('id_md_cust')->nullable();
            $table->string('kapal')->nullable();
            $table->string('title')->nullable();
            $table->string('no_jo')->nullable();
            $table->string('vendor_number')->nullable();
            $table->string('po_number')->nullable();
            $table->string('sa_gr_number')->nullable();
            $table->string('ba_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->date('tgl_invoice')->nullable();
            $table->date('due_date')->nullable();
            $table->date('eta')->nullable();
            $table->date('etd')->nullable();
            $table->string('no_voyage')->nullable();
            $table->string('grt')->nullable();
            $table->string('jetty')->nullable();
            $table->string('cond_of_payment')->nullable();
            $table->string('currency')->nullable();
            $table->string('period')->nullable();
            $table->string('bar_code')->nullable();
            $table->string('to_customer')->nullable();
            $table->text('address')->nullable();
            $table->string('att')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('mobile_phone')->nullable();
            $table->string('status')->default('draft');
            $table->text('close_note')->nullable();
            $table->text('reopen_note')->nullable();
            $table->unsignedBigInteger('id_md_chart_of_account')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('f04_invoice_pembayaran_tramper_item', function (Blueprint $table) {
            $table->id('id_invoice_tramper_item');
            $table->unsignedBigInteger('id_invoice_tramper');
            $table->string('id_jo_tram_item')->nullable();
            $table->string('id_jo_tram_pajak')->nullable();
            $table->string('item_type')->default('item'); // 'item' or 'tax'
            $table->unsignedBigInteger('id_md_chart_of_account')->nullable();
            $table->decimal('nominal_invoice', 20, 2)->default(0);
            $table->boolean('is_reimbursement')->default(false);
            $table->text('deskripsi')->nullable();
            $table->timestamps();

            $table->foreign('id_invoice_tramper')
                ->references('id_invoice_tramper')
                ->on('f03_invoice_pembayaran_tramper')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('f04_invoice_pembayaran_tramper_item');
        Schema::dropIfExists('f03_invoice_pembayaran_tramper');
    }
};
