<?php

namespace App\Models\Data;

use App\Models\Master\ChartOfAccount;
use Illuminate\Database\Eloquent\Model;

class InvoicePembayaranOtherItem extends Model
{
    protected $table = 'f06_invoice_pembayaran_other_item';
    protected $primaryKey = 'id_invoice_other_item';

    protected $fillable = [
        'id_invoice_other', 'id_jo_other_item', 'item_type', 'id_jo_other_pajak',
        'id_md_chart_of_account', 'deskripsi', 'nominal_invoice', 'nominal_invoice_usd', 'is_reimbursement',
    ];

    protected $casts = [
        'is_reimbursement' => 'boolean',
        'nominal_invoice'  => 'decimal:2',
    ];

    public function invoiceOther()
    {
        return $this->belongsTo(InvoicePembayaranOther::class, 'id_invoice_other', 'id_invoice_other');
    }

    public function joOtherItem()
    {
        return $this->belongsTo(JoOtherItem::class, 'id_jo_other_item', 'id_jo_other_item');
    }

    public function joOtherPajak()
    {
        return $this->belongsTo(JoOtherPajak::class, 'id_jo_other_pajak', 'id_jo_other_pajak');
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'id_md_chart_of_account', 'id_md_chart_of_account');
    }
}
