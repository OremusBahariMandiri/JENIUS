<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'a04_md_invoice';

    protected $fillable = [
        'id_md_invoice',
        'code',
        'invoice_ctg',
        'invoice_typ',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function joContractItems()
    {
        return $this->hasMany(\App\Models\Data\JoContractItem::class, 'id_md_invoice', 'id_md_invoice');
    }
}