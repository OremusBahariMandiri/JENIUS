<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

class LpjGen extends Model
{
    protected $table      = 'd10_lpj_gen';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_lpj_gen',
        'no_lpj_gen',
        'date',
        'amount',
        'note',
        'evidence',
    ];

    protected $casts = [
        'date'       => 'date',
        'amount'     => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /** Pivot kasbon yang masuk ke LPJ ini */
    public function kasbons()
    {
        return $this->hasMany(LpjGenKasbon::class, 'id_lpj_gen', 'id_lpj_gen');
    }

    /** Detail item LPJ (amount_lpj + COA) */
    public function items()
    {
        return $this->hasMany(LpjGenItem::class, 'id_lpj_gen', 'id_lpj_gen');
    }
}