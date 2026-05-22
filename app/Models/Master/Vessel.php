<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Vessel extends Model
{
    protected $table = 'a05_md_vessel';

    protected $fillable = [
        'id_md_vessel',
        'vessel_name',
        'vessel_type',
        'no_imo',
        'no_mmsi',
        'call_sign',
        'gt',
        'dwt',
        'year_built',
        'breadth',
        'loa',
        'flag',
        'ga',
        'ship_particular',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    // Contoh: jika ada relasi dengan tabel lain
    // public function joContracts()
    // {
    //     return $this->hasMany(\App\Models\Data\JoContract::class, 'id_md_vessel', 'id_md_vessel');
    // }
}