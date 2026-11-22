<?php

namespace App\Models\Data;

use App\Models\Master\Pajak;
use Illuminate\Database\Eloquent\Model;

class JoOtherPajak extends Model
{
    protected $table = 'b09_jo_other_pajak';
    protected $primaryKey = 'id_jo_other_pajak';

    protected $fillable = [
        'id_jo_other',
        'id_md_pajak',
        'nama_pajak',
        'persentase',
        'dasar_pengenaan',
        'dasar_pengenaan_hpp',
        'nominal_pajak',
        'nominal_pajak_hpp',
        'include_selling',
        'include_hpp',
    ];

    protected $casts = [
        'persentase'          => 'decimal:2',
        'dasar_pengenaan'     => 'decimal:2',
        'dasar_pengenaan_hpp' => 'decimal:2',
        'nominal_pajak'       => 'decimal:2',
        'nominal_pajak_hpp'   => 'decimal:2',
        'include_selling'     => 'boolean',
        'include_hpp'         => 'boolean',
    ];

    public function joOther()
    {
        return $this->belongsTo(JoOther::class, 'id_jo_other', 'id_jo_other');
    }

    public function masterPajak()
    {
        return $this->belongsTo(Pajak::class, 'id_md_pajak', 'id_md_pajak');
    }
}
