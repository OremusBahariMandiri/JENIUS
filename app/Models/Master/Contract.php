<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $table = 'a02_md_contract';

    protected $fillable = [
        'id_md_cont',
        'no_contract',
        'contract',
        'id_md_cust',
        'expenditure',
        'date_start',
        'date_end',
        'note',
    ];

    protected $casts = [
        'expenditure' => 'decimal:2',
        'date_start' => 'date',
        'date_end' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_md_cust', 'id_md_cust');
    }

    public function joContracts()
    {
        return $this->hasMany(\App\Models\Data\JoContract::class, 'id_md_cont', 'id_md_cont');
    }
}