<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use App\Models\Data\JoOther;

class LpjOther extends Model
{
    protected $table = 'd07_lpj_other';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_lpj_other',
        'no_lpj_other',
        'id_jo_other',
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

    public function joOther()
    {
        return $this->belongsTo(JoOther::class, 'id_jo_other', 'id_jo_other');
    }

    public function kasbons()
    {
        return $this->hasMany(LpjKasbonOther::class, 'id_lpj_other', 'id_lpj_other');
    }

    public function items()
    {
        return $this->hasMany(LpjOtherItem::class, 'id_lpj_other', 'id_lpj_other');
    }
}