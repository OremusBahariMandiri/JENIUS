<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoContractItem extends Model
{
    protected $table = 'b02_jo_cont_item';
    protected $primaryKey = 'id_jo_cont_item';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_jo_cont_item',
        'id_jo_cont',
        'id_md_invoice',
        'pendapatan_idr',
        'pendapatan_usd',
        'kurs_usd',
        'tgl_kurs_usd',
        'hpp_ops',
        'hargajual_idr',
        'note',
    ];

    protected $casts = [
        'pendapatan_idr' => 'decimal:2',
        'pendapatan_usd' => 'decimal:2',
        'kurs_usd' => 'decimal:2',
        'hpp_ops' => 'decimal:2',
        'hargajual_idr' => 'decimal:2',
        'tgl_kurs_usd' => 'datetime', // UBAH DARI 'date' JADI 'datetime'
    ];

    // Relationships
    public function joContract()
    {
        return $this->belongsTo(JoContract::class, 'id_jo_cont', 'id_jo_cont');
    }

    public function invoice()
    {
        return $this->belongsTo(\App\Models\Master\Invoice::class, 'id_md_invoice', 'id_md_invoice');
    }
}