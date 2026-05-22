<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Port extends Model
{
    protected $table = 'a06_md_port';

    protected $fillable = [
        'id_md_port',
        'no_port',
        'name_port',
        'alamat',
        'kota',
        'provinsi',
        'negara',
        'port_type',
        'operator_port',
        'draft',
        'panjang_kapal',
        'lebar_kapal',
        'dwt',
        'panjang_dermaga',
        'pasang_surut',
        'jam_ops',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    // Contoh: jika ada relasi dengan tabel lain
    // public function joContracts()
    // {
    //     return $this->hasMany(\App\Models\Data\JoContract::class, 'id_md_port', 'id_md_port');
    // }
}