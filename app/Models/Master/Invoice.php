<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'a04_md_invoice';
    protected $primaryKey = 'id_md_invoice';
    public $incrementing = false;
    protected $keyType = 'string';

    // Valid values for jo_ctg
    const JO_CTG_OPTIONS = [
        'contract' => 'Contract',
        'tramper'  => 'Tramper',
        'other'    => 'Other',
    ];

    protected $fillable = [
        'id_md_invoice',
        'code',
        'invoice_ctg',
        'invoice_typ',
        'jo_ctg',
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

    // Accessor: label for jo_ctg
    public function getJoCtgLabelAttribute(): string
    {
        return self::JO_CTG_OPTIONS[$this->jo_ctg] ?? ucfirst($this->jo_ctg ?? '');
    }
}