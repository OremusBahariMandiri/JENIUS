<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

class LpjGenKasbon extends Model
{
    protected $table      = 'd11_lpj_gen_kasbon';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_lpj_gen_kasbon',
        'id_lpj_gen',
        'id_kasbon_gen',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lpjGen()
    {
        return $this->belongsTo(LpjGen::class, 'id_lpj_gen', 'id_lpj_gen');
    }

    public function kasbonGen()
    {
        return $this->belongsTo(KasbonGen::class, 'id_kasbon_gen', 'id_kasbon_gen');
    }
}