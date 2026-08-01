<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

class LpjContractItem extends Model
{
    protected $table = 'd03_lpj_cont_item';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_lpj_cont_item',
        'id_lpj_cont',
        'id_kasbon_cont_item',
        'id_jo_cont_item',
        'amount_lpj',
        'id_md_chart_of_account',
    ];

    protected $casts = [
        'amount_lpj' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lpjContract()
    {
        return $this->belongsTo(LpjContract::class, 'id_lpj_cont', 'id_lpj_cont');
    }

    public function kasbonContractItem()
    {
        return $this->belongsTo(KasbonContractItem::class, 'id_kasbon_cont_item', 'id_kasbon_cont_item');
    }

    public function joContractItem()
    {
        return $this->belongsTo(JoContractItem::class, 'id_jo_cont_item', 'id_jo_cont_item');
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
