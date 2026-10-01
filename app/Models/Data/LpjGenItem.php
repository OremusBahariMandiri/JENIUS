<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

class LpjGenItem extends Model
{
    protected $table      = 'd12_lpj_gen_item';
    protected $primaryKey = 'id';
    public $incrementing  = true;
    protected $keyType    = 'int';

    protected $fillable = [
        'id_lpj_gen_item',
        'id_lpj_gen',
        'id_kasbon_gen_item',
        'amount_lpj',
        'id_md_chart_of_account',
        'origin_lpj_gen',
    ];

    protected $casts = [
        'amount_lpj' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lpjGen()
    {
        return $this->belongsTo(LpjGen::class, 'id_lpj_gen', 'id_lpj_gen');
    }

    public function kasbonGenItem()
    {
        return $this->belongsTo(KasbonGenItem::class, 'id_kasbon_gen_item', 'id_kasbon_gen_item');
    }

    public function chartOfAccount()
    {
        return $this->belongsTo(
            \App\Models\Master\ChartOfAccount::class,
            'id_md_chart_of_account',
            'id_md_chart_of_account'
        );
    }
}