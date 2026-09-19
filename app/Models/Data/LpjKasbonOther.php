<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LpjKasbonOther extends Model
{
    use SoftDeletes;

    protected $table = 'd08_lpj_kasbon_other';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_lpj_kasbon_other',
        'id_lpj_other',
        'id_kasbon_other',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function lpjOther()
    {
        return $this->belongsTo(LpjOther::class, 'id_lpj_other', 'id_lpj_other');
    }

    public function kasbonOther()
    {
        return $this->belongsTo(KasbonOther::class, 'id_kasbon_other', 'id_kasbon_other');
    }
}