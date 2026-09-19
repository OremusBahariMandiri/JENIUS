<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;

class LpjOtherItem extends Model
{
    protected $table = 'd09_lpj_other_item';
    protected $primaryKey = 'id';
    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_lpj_other_item',
        'id_lpj_other',
        'id_kasbon_other_item',
        'id_jo_other_item',
        'amount_lpj',
        'id_md_chart_of_account',
    ];

    protected $casts = [
        'amount_lpj' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function lpjOther()
    {
        return $this->belongsTo(LpjOther::class, 'id_lpj_other', 'id_lpj_other');
    }

    public function kasbonOtherItem()
    {
        return $this->belongsTo(KasbonOtherItem::class, 'id_kasbon_other_item', 'id_kasbon_other_item');
    }

    public function joOtherItem()
    {
        return $this->belongsTo(JoOtherItem::class, 'id_jo_other_item', 'id_jo_other_item');
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