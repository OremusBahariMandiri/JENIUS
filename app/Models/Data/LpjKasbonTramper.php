<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LpjKasbonTramper extends Model
{
    use SoftDeletes;

    protected $table = 'd05_lpj_kasbon_tram';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_lpj_kasbon_tram',
        'id_lpj_tram',
        'id_kasbon_tram',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function lpjTramper()
    {
        return $this->belongsTo(LpjTramper::class, 'id_lpj_tram', 'id_lpj_tram');
    }

    public function kasbonTramper()
    {
        return $this->belongsTo(KasbonTramper::class, 'id_kasbon_tram', 'id_kasbon_tram');
    }
}