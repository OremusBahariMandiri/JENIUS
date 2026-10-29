<?php

namespace App\Models\Data;

use App\Models\Master\ChartOfAccount;
use Illuminate\Database\Eloquent\Model;

class MutasiPenerimaanVoucher extends Model
{
    protected $table      = 'e05_mutasi_penerimaan_voucher';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_mutasi_penerimaan',
        'nomor_voucher',
        'keterangan',
        'tanggal',
        'id_md_chart_of_account',
        'urutan',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'urutan'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ──

    public function mutasiPenerimaan()
    {
        return $this->belongsTo(MutasiPenerimaan::class, 'id_mutasi_penerimaan', 'id');
    }

    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'id_md_chart_of_account', 'id_md_chart_of_account');
    }

    public function details()
    {
        return $this->hasMany(MutasiPenerimaanDetail::class, 'id_mutasi_voucher', 'id');
    }

    // ── Computed totals ──

    public function getTotalNilaiAttribute(): float
    {
        return $this->details->sum('nilai');
    }
}