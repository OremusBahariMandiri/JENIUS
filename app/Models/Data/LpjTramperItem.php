<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

class LpjTramperItem extends Model
{
    protected $table = 'd06_lpj_tram_item';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_lpj_tram_item',
        'id_lpj_tram',
        'id_kasbon_tram_item',
        'id_jo_tram_item',
        'amount_lpj',
        'id_md_chart_of_account',
    ];

    protected $casts = [
        'amount_lpj' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lpjTramper()
    {
        return $this->belongsTo(LpjTramper::class, 'id_lpj_tram', 'id_lpj_tram');
    }

    public function kasbonTramperItem()
    {
        return $this->belongsTo(KasbonTramperItem::class, 'id_kasbon_tram_item', 'id_kasbon_tram_item');
    }

    public function joTramperItem()
    {
        return $this->belongsTo(JoTramperItem::class, 'id_jo_tram_item', 'id_jo_tram_item');
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