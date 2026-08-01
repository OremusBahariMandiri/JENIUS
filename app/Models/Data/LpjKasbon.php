<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

class LpjKasbon extends Model
{
    protected $table = 'd02_lpj_kasbon';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_lpj_kasbon',
        'id_lpj_cont',
        'id_kasbon_cont',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lpjContract()
    {
        return $this->belongsTo(LpjContract::class, 'id_lpj_cont', 'id_lpj_cont');
    }

    public function kasbonContract()
    {
        return $this->belongsTo(KasbonContract::class, 'id_kasbon_cont', 'id_kasbon_cont');
    }
}