<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CostType extends Model
{
    protected $table = 'a11_md_cost_type';
    protected $primaryKey = 'id_md_cost_type';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_md_cost_type',
        'name',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}