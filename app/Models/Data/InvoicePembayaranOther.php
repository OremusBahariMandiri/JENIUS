<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\ChartOfAccount;

class InvoicePembayaranOther extends Model
{
    protected $table = 'f05_invoice_pembayaran_other';
    protected $primaryKey = 'id_invoice_other';

    protected $fillable = [
        'no_invoice', 'id_jo_other', 'id_md_cust', 'kapal', 'title', 'no_jo',
        'vendor_number', 'po_number', 'sa_gr_number', 'ba_number',
        'vat_number', 'tgl_invoice', 'due_date', 'eta', 'etd', 'no_voyage', 'grt', 'jetty',
        'cond_of_payment', 'currency', 'period', 'bar_code', 'to_customer', 'address',
        'att', 'office_phone', 'mobile_phone', 'status', 'created_by',
        'close_note', 'reopen_note', 'id_md_chart_of_account',
        'kurs_invoice', 'tgl_kurs_invoice',
    ];

    protected $casts = [
        'tgl_invoice' => 'date',
        'due_date'    => 'date',
        'eta'         => 'date',
        'etd'         => 'date',
    ];

    public function items()
    {
        return $this->hasMany(InvoicePembayaranOtherItem::class, 'id_invoice_other', 'id_invoice_other');
    }

    public function joOther()
    {
        return $this->belongsTo(JoOther::class, 'id_jo_other', 'id_jo_other');
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Master\Customer::class, 'id_md_cust', 'id_md_cust');
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'id_md_chart_of_account', 'id_md_chart_of_account');
    }
}
