<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\Contract;
use App\Models\Master\Area;

class JoContract extends Model
{
    protected $table = 'b01_jo_cont';
    protected $primaryKey = 'id_jo_cont';
    public $incrementing = false; // ← UBAH JADI FALSE (manual ID)
    protected $keyType = 'int';

    protected $fillable = [
        'id_jo_cont',
        'no_jo_cont',
        'tgl_jo_cont',
        'id_md_cont',
        'id_md_area',
        'title',
        'note',
    ];

    protected $casts = [
        'tgl_jo_cont' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function contract()
    {
        return $this->belongsTo(Contract::class, 'id_md_cont', 'id_md_cont');
    }

    public function area()
    {
        return $this->belongsTo(Area::class, 'id_md_area', 'id_md_area');
    }

    public function items()
    {
        return $this->hasMany(JoContractItem::class, 'id_jo_cont', 'id_jo_cont');
    }
}