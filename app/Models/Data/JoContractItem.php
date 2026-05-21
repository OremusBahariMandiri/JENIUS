<?php

namespace App\Models\Data;

use App\Helpers\IdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\Invoice;

class JoContractItem extends Model
{
    use SoftDeletes;

    protected $table = 'b02_jo_cont_item';
    protected $primaryKey = 'id_jo_cont_item';

    protected $fillable = [
        'id_jo_cont_item',
        'id_jo_cont',
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
        'kurs_usd' => 'decimal:2',
        'tgl_kurs_usd' => 'date',
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
            if (empty($model->id_jo_cont_item)) {
                $model->id_jo_cont_item = IdGenerator::generate(
                    'B02',                  // Kode tabel
                    'b02_jo_cont_item',     // Nama tabel
                    'id_jo_cont_item'       // Nama kolom
                );
            }
        });
    }

    // Relationships
    public function joContract()
    {
        return $this->belongsTo(JoContract::class, 'id_jo_cont', 'id_jo_cont');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'id_md_invoice', 'id_md_invoice');
    }
}