<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasbonOtherItem extends Model
{
    use SoftDeletes;

    protected $table      = 'c06_kasbon_other_item';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_kasbon_other_item',
        'id_kasbon_other',
        'id_jo_other_item',
        'nilai_hpp_other_item',
        'nilai_kasbon',
        'total_kasbon',
    ];

    protected $casts = [
        'nilai_hpp_other_item' => 'decimal:2',
        'nilai_kasbon'         => 'decimal:2',
        'total_kasbon'         => 'decimal:2',
        'created_at'           => 'datetime',
        'updated_at'           => 'datetime',
        'deleted_at'           => 'datetime',
    ];

    public function kasbonOther()
    {
        return $this->belongsTo(KasbonOther::class, 'id_kasbon_other', 'id_kasbon_other');
    }

    public function joOtherItem()
    {
        return $this->belongsTo(JoOtherItem::class, 'id_jo_other_item', 'id_jo_other_item');
    }
}