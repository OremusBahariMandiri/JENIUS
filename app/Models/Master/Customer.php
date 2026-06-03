<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $table = 'a01_md_customer';
    protected $primaryKey = 'id_md_cust'; // ← tambahkan ini
    public $incrementing = false;          // ← jika tidak pakai auto-increment DB
    protected $keyType = 'string';         // ← tambahkan ini

    protected $fillable = [
        'id_md_cust',
        'code',
        'customer',
        'address',
        'phone',
        'email',
        'website',
        'npwp',
        'note',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function contracts()
    {
        return $this->hasMany(Contract::class, 'id_md_cust', 'id_md_cust');
    }
}