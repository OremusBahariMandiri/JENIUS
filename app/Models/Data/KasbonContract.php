<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Data\JoContract;
use App\Models\Master\Departemen;
use App\Models\Master\Branch;
use App\Models\Master\Release;
use App\Models\Master\ReleaseTo;

class KasbonContract extends Model
{
    use SoftDeletes;

    protected $table = 'c01_kasbon_cont';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_kasbon_cont',
        'id_jo_cont',
        'id_md_dep',
        'id_md_cabang',
        'id_md_release',
        'nomor',
        'tgl_kasbon',
        'tgl_release',
        'note',
        'priority',
        'due_date',
        'ca_release_status',
        'ca_release_date',
    ];

    protected $casts = [
        'tgl_kasbon'  => 'date',
        'tgl_release' => 'date',
        'ca_release_date'    => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
        'due_date'    => 'datetime',
    ];

    // Relationships
    public function joContract()
    {
        return $this->belongsTo(JoContract::class, 'id_jo_cont', 'id_jo_cont');
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
        return $this->hasMany(KasbonContractItem::class, 'id_kasbon_cont', 'id_kasbon_cont');
    }
}
