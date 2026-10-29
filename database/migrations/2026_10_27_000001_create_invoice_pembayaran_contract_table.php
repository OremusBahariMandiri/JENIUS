<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('f01_invoice_pembayaran_contract', function (Blueprint $table) {
            $table->id('id_invoice_contract');
            $table->string('no_invoice')->unique();
            $table->unsignedBigInteger('id_jo_cont')->nullable();
            $table->string('id_md_cust')->nullable();
            $table->string('kapal')->nullable();
            $table->string('title')->nullable();
            $table->string('no_jo')->nullable();
            $table->string('vendor_number')->nullable();
            $table->string('contract_no')->nullable();
            $table->string('po_number')->nullable();
            $table->string('sa_gr_number')->nullable();
            $table->string('ba_number')->nullable();
            $table->string('vat_number')->nullable();
            $table->date('tgl_invoice')->nullable();
            $table->date('due_date')->nullable();
            $table->date('eta')->nullable();
            $table->date('etd')->nullable();
            $table->string('no_voyage')->nullable();
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
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        Schema::create('f02_invoice_pembayaran_contract_item', function (Blueprint $table) {
            $table->id('id_invoice_contract_item');
            $table->unsignedBigInteger('id_invoice_contract');
            $table->unsignedBigInteger('id_jo_cont_item')->nullable();
            $table->unsignedBigInteger('id_md_chart_of_account')->nullable();
            $table->decimal('nominal_invoice', 20, 2)->default(0);
            $table->boolean('is_reimbursement')->default(false);
            $table->timestamps();

            $table->foreign('id_invoice_contract')
                ->references('id_invoice_contract')
                ->on('f01_invoice_pembayaran_contract')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('f02_invoice_pembayaran_contract_item');
        Schema::dropIfExists('f01_invoice_pembayaran_contract');
    }
};
