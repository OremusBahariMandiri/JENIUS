<?php

namespace App\Models\Data;

use App\Helpers\IdGenerator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\Pajak;

class KasbonGenPajak extends Model
{
    use SoftDeletes;

    protected $table      = 'c09_kasbon_gen_pajak';
    protected $primaryKey = 'id_kasbon_gen_pajak';
    public $incrementing  = false;
    protected $keyType    = 'string';

    protected $fillable = [
        'id_kasbon_gen_pajak',
        'id_kasbon_gen',
        'id_md_pajak',
        'nama_pajak',
        'persentase',
        'dasar_pengenaan',
        'nominal_pajak',
    ];

    protected $casts = [
        'persentase'      => 'decimal:2',
        'dasar_pengenaan' => 'decimal:2',
        'nominal_pajak'   => 'decimal:2',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
        'deleted_at'      => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id_kasbon_gen_pajak)) {
                $model->id_kasbon_gen_pajak = IdGenerator::generate(
                    'C09',
                    'c09_kasbon_gen_pajak',
                    'id_kasbon_gen_pajak'
                );
            }
        });
    }

    public function kasbonGen()
    {
        return $this->belongsTo(KasbonGen::class, 'id_kasbon_gen', 'id_kasbon_gen');
    }

    public function pajak()
    {
        return $this->belongsTo(Pajak::class, 'id_md_pajak', 'id_md_pajak');
    }
}
