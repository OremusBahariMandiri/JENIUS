<?php

namespace App\Models\Data;

use App\Models\Master\ChartOfAccount;
use Illuminate\Database\Eloquent\Model;

class MutasiPembayaranVoucher extends Model
{
    protected $table      = 'e02_mutasi_pembayaran_voucher';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_mutasi_pembayaran',
        'nomor_voucher',
        'keterangan',
        'tgl_keluar',
        'id_md_chart_of_account',
        'urutan',
    ];

    protected $casts = [
        'tgl_keluar'  => 'date',
        'urutan'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ──

    public function mutasiPembayaran()
    {
        return $this->belongsTo(MutasiPembayaran::class, 'id_mutasi_pembayaran', 'id');
    }

    public function coa()
    {
        return $this->belongsTo(
            ChartOfAccount::class,
            'id_md_chart_of_account',
            'id_md_chart_of_account'
        );
    }

    public function details()
    {
        return $this->hasMany(MutasiPembayaranDetail::class, 'id_mutasi_voucher', 'id');
    }

    // ── Computed totals ──

    public function getTotalNilaiAttribute(): float
    {
        return $this->details->sum('nilai');
    }

    public function getTotalPjAttribute(): float
    {
        return $this->details->sum('pj');
    }

    public function getTotalSelisihAttribute(): float
    {
        return $this->details->sum('selisih');
    }
}
