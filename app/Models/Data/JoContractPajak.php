<?php

namespace App\Models\Data;

use App\Models\Master\Pajak;
use Illuminate\Database\Eloquent\Model;

class JoContractPajak extends Model
{
    protected $table = 'b07_jo_cont_pajak';
    protected $primaryKey = 'id_jo_cont_pajak';

    protected $fillable = [
        'id_jo_cont',
        'id_md_pajak',
        'nama_pajak',
        'persentase',
        'dasar_pengenaan',
        'nominal_pajak',
        'include_selling',
        'include_hpp',
    ];

    protected $casts = [
        'persentase'     => 'decimal:2',
        'dasar_pengenaan'=> 'decimal:2',
        'nominal_pajak'  => 'decimal:2',
        'include_selling'=> 'boolean',
        'include_hpp'    => 'boolean',
    ];

    public function joContract()
    {
        return $this->belongsTo(JoContract::class, 'id_jo_cont', 'id_jo_cont');
    }

    public function masterPajak()
    {
        return $this->belongsTo(Pajak::class, 'id_md_pajak', 'id_md_pajak');
    }
}
