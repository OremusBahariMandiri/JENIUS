<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\Customer;
use App\Models\Master\Port;

class JoTramper extends Model
{
    use SoftDeletes;

    protected $table = 'b03_jo_tram';
    protected $primaryKey = 'id_jo_tram';
    public $incrementing = true; // Auto-increment
    protected $keyType = 'int'; // Type integer

    protected $fillable = [
        'id_jo_tram',
        'id_md_cust',
        'id_md_port',
        'date_start',
        'date_end',
        'title',
        'note',
        'sts_proses',
    ];

    protected $casts = [
        'date_start' => 'date',
        'date_end' => 'date',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'id_md_cust', 'id_md_cust');
    }

    public function port()
    {
        return $this->belongsTo(Port::class, 'id_md_port', 'id_md_port');
    }

    public function items()
    {
        return $this->hasMany(JoTramperItem::class, 'id_jo_tram', 'id_jo_tram');
    }
}