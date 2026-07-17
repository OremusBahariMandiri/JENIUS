<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\Departemen;
use App\Models\Master\Branch;
use App\Models\Master\ReleaseTo;

class KasbonOther extends Model
{
    use SoftDeletes;

    protected $table      = 'c05_kasbon_other';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_kasbon_other',
        'id_jo_other',
        'id_md_dep',
        'id_md_cabang',
        'id_md_release',
        'nomor',
        'tgl_kasbon',
        'tgl_release',
        'note',
        'priority',
        'due_date',
    ];

    protected $casts = [
        'tgl_kasbon'  => 'date',
        'tgl_release' => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
        'due_date'    => 'datetime',
    ];

    public function joOther()
    {
        return $this->belongsTo(JoOther::class, 'id_jo_other', 'id_jo_other');
    }

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
        return $this->hasMany(KasbonOtherItem::class, 'id_kasbon_other', 'id_kasbon_other');
    }
}
