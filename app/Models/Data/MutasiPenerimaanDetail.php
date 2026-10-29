<?php

namespace App\Models\Data;

use App\Models\Master\Customer;
use App\Models\Master\Departemen;
use App\Models\Master\Branch;
use Illuminate\Database\Eloquent\Model;

class MutasiPenerimaanDetail extends Model
{
    protected $table      = 'e06_mutasi_penerimaan_detail';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    const TIPE_UANG_MUKA    = 'uang_muka_pembayaran';
    const TIPE_PENERIMAAN   = 'penerimaan_pembayaran';
    const TIPE_PENGEMBALIAN = 'pengembalian_lebih_bayar';

    protected $fillable = [
        'id_mutasi_voucher',
        'jenis',            // tramper | other | contract | general
        'tipe_pembayaran',  // uang_muka_pembayaran | penerimaan_pembayaran | pengembalian_lebih_bayar

        // JO refs (hanya 1 terisi sesuai jenis)
        'id_jo_tram',
        'id_jo_other',
        'id_jo_cont',

        // Kasbon refs (terisi jika tipe = pengembalian)
        'id_kasbon_tram',
        'id_kasbon_other',
        'id_kasbon_cont',
        'id_kasbon_gen',

        // Auto-fill dari JO / Kasbon
        'id_md_cust',
        'id_md_dep',
        'id_md_branch',

        // Nilai — COA pakai COA voucher, bukan per detail
        'release_to',
        'nilai',
        'keterangan',
    ];

    protected $casts = [
        'nilai'      => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // ── Relationships ──

    public function voucher()
    {
        return $this->belongsTo(MutasiPenerimaanVoucher::class, 'id_mutasi_voucher', 'id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_md_cust', 'id_md_cust');
    }

    public function departemen()
    {
        return $this->belongsTo(Departemen::class, 'id_md_dep', 'id_md_dep');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'id_md_branch', 'id_md_branch');
    }

    // ── JO Relations ──

    public function joTramper()
    {
        return $this->belongsTo(JoTramper::class, 'id_jo_tram', 'id_jo_tram');
    }

    public function joOther()
    {
        return $this->belongsTo(JoOther::class, 'id_jo_other', 'id_jo_other');
    }

    public function joContract()
    {
        return $this->belongsTo(JoContract::class, 'id_jo_cont', 'id_jo_cont');
    }

    // ── Kasbon Relations ──

    public function kasbonTramper()
    {
        return $this->belongsTo(KasbonTramper::class, 'id_kasbon_tram', 'id');
    }

    public function kasbonOther()
    {
        return $this->belongsTo(KasbonOther::class, 'id_kasbon_other', 'id');
    }

    public function kasbonContract()
    {
        return $this->belongsTo(KasbonContract::class, 'id_kasbon_cont', 'id');
    }

    public function kasbonGen()
    {
        return $this->belongsTo(KasbonGen::class, 'id_kasbon_gen', 'id');
    }

    // ── Helpers ──

    public function getJoAttribute()
    {
        return match ($this->jenis) {
            'tramper'  => $this->joTramper,
            'other'    => $this->joOther,
            'contract' => $this->joContract,
            default    => null,
        };
    }

    public function getKasbonAttribute()
    {
        return match ($this->jenis) {
            'tramper'  => $this->kasbonTramper,
            'other'    => $this->kasbonOther,
            'contract' => $this->kasbonContract,
            'general'  => $this->kasbonGen,
            default    => null,
        };
    }

    public function isPengembalian(): bool
    {
        return $this->tipe_pembayaran === self::TIPE_PENGEMBALIAN;
    }
}
