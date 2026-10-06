<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use App\Models\Master\ChartOfAccount;

class MutasiPembayaran extends Model
{
    protected $table      = 'e01_mutasi_pembayaran';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'nomor',
        'id_md_chart_of_account',
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


    public function coa()
    {
        return $this->belongsTo(ChartOfAccount::class, 'id_md_chart_of_account', 'id_md_chart_of_account');
    }

    public function vouchers()
    {
        return $this->hasMany(MutasiPembayaranVoucher::class, 'id_mutasi_pembayaran', 'id')
            ->orderBy('urutan');
    }

    // Shortcut: semua detail lintas voucher
    public function details()
    {
        return $this->hasManyThrough(
            MutasiPembayaranDetail::class,
            MutasiPembayaranVoucher::class,
            'id_mutasi_pembayaran', // FK di voucher → mutasi
            'id_mutasi_voucher',    // FK di detail → voucher
            'id',                   // local key di mutasi
            'id'                    // local key di voucher
        );
    }

    // Alias supaya bisa dipanggil ->chartOfAccount juga
    public function chartOfAccount()
    {
        return $this->belongsTo(ChartOfAccount::class, 'id_md_chart_of_account', 'id_md_chart_of_account');
    }
}
