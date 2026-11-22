<?php

namespace App\Models\Data;

use App\Models\Master\ChartOfAccount;
use Illuminate\Database\Eloquent\Model;

class InvoicePembayaranTramperItem extends Model
{
    protected $table = 'f04_invoice_pembayaran_tramper_item';
    protected $primaryKey = 'id_invoice_tramper_item';

    protected $fillable = [
        'id_invoice_tramper', 'id_jo_tram_item', 'item_type', 'id_jo_tram_pajak',
        'id_md_chart_of_account', 'deskripsi', 'nominal_invoice', 'nominal_invoice_usd',
        'is_reimbursement',
    ];

    protected $casts = [
        'is_reimbursement'    => 'boolean',
        'nominal_invoice'     => 'decimal:2',
        'nominal_invoice_usd' => 'decimal:4',
    ];

    public function invoiceTramper()
    {
        return $this->belongsTo(InvoicePembayaranTramper::class, 'id_invoice_tramper', 'id_invoice_tramper');
    }

    public function joTramperItem()
    {
        return $this->belongsTo(JoTramperItem::class, 'id_jo_tram_item', 'id_jo_tram_item');
    }

    public function joTramperPajak()
    {
        return $this->belongsTo(JoTramperPajak::class, 'id_jo_tram_pajak', 'id_jo_tram_pajak');
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'id_md_chart_of_account', 'id_md_chart_of_account');
    }
}
