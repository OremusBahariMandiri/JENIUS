<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ChartOfAccount extends Model
{
    protected $table = 'a13_md_chart_of_account';
    protected $primaryKey = 'id_md_chart_of_account';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_md_chart_of_account',
        'parrent',
        'no_account',
        'account_name',
        'type',
        'payment_type',
        'opening_balance',
        'current_balance',
    ];

    protected $casts = [
        'opening_balance' => 'decimal:2',
        'current_balance' => 'decimal:2',
        'created_at'      => 'datetime',
        'updated_at'      => 'datetime',
    ];

    /**
     * Relasi ke ParentChartOfAccount (a12) untuk ambil kode perkiraan & tipe akun.
     */
    public function parentAccount()
    {
        return $this->belongsTo(ParentChartOfAccount::class, 'parrent', 'kode_perkiraan');
    }

    /**
     * Shortcut ambil cost type lewat parent.
     * Penggunaan: $coa->cost_type->name
     */
    public function getCostTypeAttribute()
    {
        return $this->parentAccount?->costType;
    }
}