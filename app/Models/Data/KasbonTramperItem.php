<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasbonTramperItem extends Model
{
    use SoftDeletes;

    protected $table      = 'c04_kasbon_tram_item';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_kasbon_tram_item',
        'id_kasbon_tram',
        'id_jo_tram_item',
        'nilai_hpp_tram_item',
        'nilai_kasbon',
        'total_kasbon',
    ];

    protected $casts = [
        'nilai_hpp_tram_item' => 'decimal:2',
        'nilai_kasbon'        => 'decimal:2',
        'total_kasbon'        => 'decimal:2',
        'created_at'          => 'datetime',
        'updated_at'          => 'datetime',
        'deleted_at'          => 'datetime',
    ];

    public function kasbonTramper()
    {
        return $this->belongsTo(KasbonTramper::class, 'id_kasbon_tram', 'id_kasbon_tram');
    }

    public function joTramperItem()
    {
        return $this->belongsTo(JoTramperItem::class, 'id_jo_tram_item', 'id_jo_tram_item');
    }
}
