<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use App\Models\Data\JoContract;

class LpjContract extends Model
{
    protected $table = 'd01_lpj_cont';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_lpj_cont',
        'no_lpj_cont',
        'id_jo_cont',
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

    public function joContract()
    {
        return $this->belongsTo(JoContract::class, 'id_jo_cont', 'id_jo_cont');
    }

    public function kasbons()
    {
        return $this->hasMany(LpjKasbon::class, 'id_lpj_cont', 'id_lpj_cont');
    }

    public function items()
    {
        return $this->hasMany(LpjContractItem::class, 'id_lpj_cont', 'id_lpj_cont');
    }
}