<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ParentChartOfAccount extends Model
{
    use HasFactory;

    protected $table = 'a12_md_parent_chart_of_account';

    protected $fillable = [
        'id_md_cost_type',
        'kode_perkiraan',
        'nama',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relasi ke CostType (a11).
     */
    public function costType()
    {
        return $this->belongsTo(CostType::class, 'id_md_cost_type', 'id_md_cost_type');
    }

    /**
     * Relasi ke ChartOfAccount (a13).
     */
    public function chartOfAccounts()
    {
        return $this->hasMany(ChartOfAccount::class, 'parrent', 'kode_perkiraan');
    }
}