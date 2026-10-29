<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\ChartOfAccount;

class MutasiPenerimaan extends Model
{
    protected $table      = 'e04_mutasi_penerimaan';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'nomor',
        'no_cek',
        'tanggal',
        'kurs',
        'memo',
        'created_by',
    ];

    protected $casts = [
        'tanggal'    => 'date',
        'kurs'       => 'decimal:4',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ──

    public function vouchers()
    {
        return $this->hasMany(MutasiPenerimaanVoucher::class, 'id_mutasi_penerimaan', 'id')
            ->orderBy('urutan');
    }

    /** Shortcut: semua detail lintas voucher */
    public function details()
    {
        return $this->hasManyThrough(
            MutasiPenerimaanDetail::class,
            MutasiPenerimaanVoucher::class,
            'id_mutasi_penerimaan',
            'id_mutasi_voucher',
            'id',
            'id'
        );
    }
}