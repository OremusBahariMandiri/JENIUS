<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Pajak extends Model
{
    protected $table = 'a14_md_pajak';
    protected $primaryKey = 'id_md_pajak';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_md_pajak',
        'nama_pajak',
        'persentase',
        'kode_pajak',
        'keterangan',
        'is_active',
    ];

    protected $casts = [
        'persentase' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    /**
     * Return display label: "PPN (11%)"
     */
    public function getLabelAttribute(): string
    {
        return $this->nama_pajak . ' (' . number_format($this->persentase, 2) . '%)';
    }
}
