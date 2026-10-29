<?php

namespace App\Models\Data;

use App\Models\Master\ChartOfAccount;
use Illuminate\Database\Eloquent\Model;

class InvoicePembayaranContractItem extends Model
{
    protected $table = 'f02_invoice_pembayaran_contract_item';
    protected $primaryKey = 'id_invoice_contract_item';

    protected $fillable = [
        'id_invoice_contract', 'id_jo_cont_item', 'item_type', 'id_jo_cont_pajak',
        'id_md_chart_of_account', 'deskripsi', 'nominal_invoice', 'is_reimbursement',
    ];

    protected $casts = [
        'is_reimbursement' => 'boolean',
        'nominal_invoice'  => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(InvoicePembayaranContract::class, 'id_invoice_contract', 'id_invoice_contract');
    }

    public function joContractItem()
    {
        return $this->belongsTo(JoContractItem::class, 'id_jo_cont_item', 'id_jo_cont_item');
    }

    public function joContractPajak()
    {
        return $this->belongsTo(JoContractPajak::class, 'id_jo_cont_pajak', 'id_jo_cont_pajak');
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'id_md_chart_of_account', 'id_md_chart_of_account');
    }
}
