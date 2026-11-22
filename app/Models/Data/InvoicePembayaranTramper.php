<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\ChartOfAccount;

class InvoicePembayaranTramper extends Model
{
    protected $table = 'f03_invoice_pembayaran_tramper';
    protected $primaryKey = 'id_invoice_tramper';

    protected $fillable = [
        'no_invoice', 'id_jo_tram', 'id_md_cust', 'kapal', 'title', 'no_jo',
        'vendor_number', 'po_number', 'sa_gr_number', 'ba_number',
        'vat_number', 'tgl_invoice', 'due_date', 'eta', 'etd', 'no_voyage', 'grt', 'jetty',
        'cond_of_payment', 'currency', 'kurs_invoice', 'tgl_kurs_invoice', 'period',
        'bar_code', 'to_customer', 'address',
        'att', 'office_phone', 'mobile_phone', 'status', 'created_by',
        'close_note', 'reopen_note', 'id_md_chart_of_account',
    ];

    protected $casts = [
        'tgl_invoice'      => 'date',
        'due_date'         => 'date',
        'eta'              => 'date',
        'etd'              => 'date',
        'tgl_kurs_invoice' => 'date',
        'kurs_invoice'     => 'decimal:2',
    ];

    public function items()
    {
        return $this->hasMany(InvoicePembayaranTramperItem::class, 'id_invoice_tramper', 'id_invoice_tramper');
    }

    public function joTramper()
    {
        return $this->belongsTo(JoTramper::class, 'id_jo_tram', 'id_jo_tram');
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
