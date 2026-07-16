<?php

namespace App\Models\Data;

use App\Helpers\IdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\Invoice;

class KasbonGenItem extends Model
{
    use SoftDeletes;

    protected $table      = 'c08_kasbon_gen_item';
    protected $primaryKey = 'id_kasbon_gen_item';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_kasbon_gen_item',
        'id_kasbon_gen',
        'id_md_invoice',
        'nilai_kasbon',
    ];

    protected $casts = [
        'nilai_kasbon' => 'decimal:2',
        'created_at'   => 'datetime',
        'updated_at'   => 'datetime',
        'deleted_at'   => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_kasbon_gen_item)) {
                $model->id_kasbon_gen_item = IdGenerator::generate(
                    'C08',
                    'c08_kasbon_gen_item',
                    'id_kasbon_gen_item'
                );
            }
        });
    }

    public function kasbonGen()
    {
        return $this->belongsTo(KasbonGen::class, 'id_kasbon_gen', 'id_kasbon_gen');
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class, 'id_md_invoice', 'id_md_invoice');
    }
}