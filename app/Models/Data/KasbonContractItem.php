<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasbonContractItem extends Model
{
    use SoftDeletes;

    protected $table = 'c02_kasbon_cont_item';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_kasbon_cont_item',
        'id_kasbon_cont',
        'id_jo_cont_item',
        'nilai_hpp_cont_item',
        'nilai_kasbon',
        'total_kasbon',
        'origin_lpj_cont',
    ];

    protected $casts = [
        'nilai_hpp_cont_item' => 'decimal:2',
        'nilai_kasbon'        => 'decimal:2',
        'total_kasbon'        => 'decimal:2',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'deleted_at'          => 'datetime',
    ];

    // Relationships
    public function kasbonContract()
    {
        return $this->belongsTo(KasbonContract::class, 'id_kasbon_cont', 'id_kasbon_cont');
    }

    public function joContractItem()
    {
        return $this->belongsTo(JoContractItem::class, 'id_jo_cont_item', 'id_jo_cont_item');
    }
}