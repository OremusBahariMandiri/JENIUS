<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

class MutasiPembayaranDetail extends Model
{
    protected $table      = 'e03_mutasi_pembayaran_detail';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_mutasi_voucher',
        'jenis',           // tramper | other | contract | general

        // JO references (nullable, hanya 1 terisi sesuai jenis, general = null semua)
        'id_jo_tram',
        'id_jo_other',
        'id_jo_cont',

        // Kasbon references (nullable, hanya 1 terisi sesuai jenis)
        'id_kasbon_tram',
        'id_kasbon_other',
        'id_kasbon_cont',
        'id_kasbon_gen',

        // Nilai finansial (snapshot saat entry, tidak ikut perubahan LPJ berikutnya)
        'nilai',
        'pj',
        'selisih',

        'keterangan',
    ];

    protected $casts = [
        'nilai'      => 'decimal:2',
        'pj'         => 'decimal:2',
        'selisih'    => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        // Auto-hitung selisih sebelum simpan
        static::creating(function ($model) {
            $model->selisih = ($model->nilai ?? 0) - ($model->pj ?? 0);
        });

        static::updating(function ($model) {
            $model->selisih = ($model->nilai ?? 0) - ($model->pj ?? 0);
        });
    }

    // ── Relationships ──

    public function voucher()
    {
        return $this->belongsTo(MutasiPembayaranVoucher::class, 'id_mutasi_voucher', 'id');
    }

    // JO relations (hanya 1 aktif sesuai jenis)
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

    // Kasbon relations (hanya 1 aktif sesuai jenis)
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

    // ── Helpers: ambil JO atau Kasbon aktif sesuai jenis ──

    /**
     * Ambil model JO yang aktif berdasarkan jenis.
     * General tidak punya JO → return null.
     */
    public function getJoAttribute()
    {
        return match ($this->jenis) {
            'tramper'  => $this->joTramper,
            'other'    => $this->joOther,
            'contract' => $this->joContract,
            default    => null,
        };
    }

    /**
     * Ambil model Kasbon yang aktif berdasarkan jenis.
     */
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
}