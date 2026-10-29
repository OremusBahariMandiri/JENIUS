<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

class InvoicePembayaranContract extends Model
{
    protected $table = 'f01_invoice_pembayaran_contract';
    protected $primaryKey = 'id_invoice_contract';

    protected $fillable = [
        'no_invoice', 'id_jo_cont', 'id_md_cust', 'kapal', 'title', 'no_jo',
        'vendor_number', 'contract_no', 'po_number', 'sa_gr_number', 'ba_number',
        'vat_number', 'tgl_invoice', 'due_date', 'eta', 'etd', 'no_voyage', 'jetty',
        'cond_of_payment', 'currency', 'period', 'bar_code', 'to_customer', 'address',
        'att', 'office_phone', 'mobile_phone', 'status', 'created_by',
        'close_note', 'reopen_note',
    ];

    protected $casts = [
        'tgl_invoice' => 'date',
        'due_date'    => 'date',
        'eta'         => 'date',
        'etd'         => 'date',
    ];

    public function items()
    {
        return $this->hasMany(InvoicePembayaranContractItem::class, 'id_invoice_contract', 'id_invoice_contract');
    }

    public function joContract()
    {
        return $this->belongsTo(JoContract::class, 'id_jo_cont', 'id_jo_cont');
    }

    public function customer()
    {
        return $this->belongsTo(\App\Models\Master\Customer::class, 'id_md_cust', 'id_md_cust');
    }
}
