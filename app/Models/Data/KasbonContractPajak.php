<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KasbonContractPajak extends Model
{
    use SoftDeletes;

    protected $table = 'c03_kasbon_cont_pajak';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_kasbon_cont_pajak',
        'id_kasbon_cont',
        'id_jo_cont_pajak',
        'nama_pajak',
        'persentase',
        'nominal_pajak',
        'include_selling',
        'include_hpp',
        'nilai_kasbon_pajak',
    ];

    protected $casts = [
        'persentase'         => 'decimal:2',
        'nominal_pajak'      => 'decimal:2',
        'nilai_kasbon_pajak' => 'decimal:2',
        'include_selling'    => 'boolean',
        'include_hpp'        => 'boolean',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        'deleted_at'         => 'datetime',
    ];

    public function kasbonContract()
    {
        return $this->belongsTo(KasbonContract::class, 'id_kasbon_cont', 'id_kasbon_cont');
    }

    public function joContractPajak()
    {
        return $this->belongsTo(JoContractPajak::class, 'id_jo_cont_pajak', 'id_jo_cont_pajak');
    }
}
