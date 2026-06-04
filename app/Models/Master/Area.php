<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    protected $table = 'a03_md_area';
    protected $primaryKey = 'id_md_area'; // ← tambahkan ini
    public $incrementing = false;          // ← tambahkan ini
    protected $keyType = 'string'; 

    protected $fillable = [
        'id_md_area',
        'code',
        'area',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function joContracts()
    {
        return $this->hasMany(\App\Models\Data\JoContract::class, 'id_md_area', 'id_md_area');
    }
}