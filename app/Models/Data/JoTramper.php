<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class JoTramper extends Model
{
    use SoftDeletes;

    protected $table      = 'b03_jo_tram';
    protected $primaryKey = 'id_jo_tram';   // ← kolom PK yang benar
    public $incrementing  = false;           // ← bukan auto-increment (string ID)
    protected $keyType    = 'string';        // ← tipe string seperti "B03062600001"

    protected $fillable = [
        'id_jo_tram',
        'id_md_cust',
        'id_md_port',
        'id_md_vessel',
        'date_start',
        'date_end',
        'title',
        'note',
        'sts_proses',
    ];

    protected $casts = [
        'date_start'  => 'date',
        'date_end'    => 'date',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
        'deleted_at'  => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(\App\Models\Master\Customer::class, 'id_md_cust', 'id_md_cust');
    }

    public function port()
    {
        return $this->belongsTo(\App\Models\Master\Port::class, 'id_md_port', 'id_md_port');
    }

    public function items()
    {
        return $this->hasMany(JoTramperItem::class, 'id_jo_tram', 'id_jo_tram');
    }

    public function vessel()
    {
        return $this->belongsTo(\App\Models\Master\Vessel::class, 'id_md_vessel', 'id_md_vessel');
    }
}
