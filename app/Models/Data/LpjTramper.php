<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use App\Models\Data\JoTramper;

class LpjTramper extends Model
{
    protected $table = 'd04_lpj_tram';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_lpj_tram',
        'no_lpj_tram',
        'id_jo_tram',
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

    public function joTramper()
    {
        return $this->belongsTo(JoTramper::class, 'id_jo_tram', 'id_jo_tram');
    }

    public function kasbons()
    {
        return $this->hasMany(LpjKasbonTramper::class, 'id_lpj_tram', 'id_lpj_tram');
    }

    public function items()
    {
        return $this->hasMany(LpjTramperItem::class, 'id_lpj_tram', 'id_lpj_tram');
    }
}