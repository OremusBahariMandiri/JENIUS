<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\Departemen;
use App\Models\Master\Branch;
use App\Models\Master\ReleaseTo;

class KasbonGen extends Model
{
    use SoftDeletes;

    protected $table      = 'c07_kasbon_gen';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_kasbon_gen',
        'id_md_dep',
        'id_md_cabang',
        'id_md_release',
        'nomor',
        'tgl_kasbon',
        'tgl_release',
        'note',
    ];

    protected $casts = [
        'tgl_kasbon'  => 'date',
        'tgl_release' => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_md_dep', 'id_md_dep');
    }

    public function cabang()
    {
        return $this->belongsTo(Branch::class, 'id_md_cabang', 'id_md_branch');
    }

    public function release()
    {
        return $this->belongsTo(ReleaseTo::class, 'id_md_release', 'id_md_release');
    }

    public function items()
    {
        return $this->hasMany(KasbonGenItem::class, 'id_kasbon_gen', 'id_kasbon_gen');
    }
}