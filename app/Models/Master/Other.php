<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Other extends Model
{
    protected $table = 'a07_md_other';
    protected $primaryKey = 'id_md_other'; // ← tambahkan ini
    public $incrementing = false;           // ← tambahkan ini
    protected $keyType = 'string'; 

    protected $fillable = [
        'id_md_other',
        'code',
        'other',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    // Contoh: jika ada relasi dengan tabel lain
    // public function joContractItems()
    // {
    //     return $this->hasMany(\App\Models\Data\JoContractItem::class, 'id_md_other', 'id_md_other');
    // }
}