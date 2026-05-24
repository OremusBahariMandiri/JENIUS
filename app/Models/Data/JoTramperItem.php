<?php

namespace App\Models\Data;

use App\Helpers\IdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\Invoice;

class JoTramperItem extends Model
{
    use SoftDeletes;

    protected $table = 'b04_jo_tram_item';
    protected $primaryKey = 'id_jo_tram_item';

    protected $fillable = [
        'id_jo_tram_item',
        'id_jo_tram',
        'id_md_invoice',
        'pendapatan_idr',
        'pendapatan_usd',
        'kurs_usd',
        'tgl_kurs_usd',
        'hpp_ops',
        'hargajual_idr',
    ];

    protected $casts = [
        'pendapatan_idr' => 'decimal:2',
        'pendapatan_usd' => 'decimal:2',
        'kurs_usd' => 'decimal:4',
        'tgl_kurs_usd' => 'datetime',
        'hpp_ops' => 'decimal:2',
        'hargajual_idr' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_jo_tram_item)) {
                $model->id_jo_tram_item = IdGenerator::generate(
                    'B04',                  // Kode tabel
                    'b04_jo_tram_item',     // Nama tabel
                    'id_jo_tram_item'       // Nama kolom
                );
            }
        });
    }

    // Relationships
    public function joTramper()
    {
        return $this->belongsTo(JoTramper::class, 'id_jo_tram', 'id_jo_tram');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'id_md_invoice', 'id_md_invoice');
    }
}