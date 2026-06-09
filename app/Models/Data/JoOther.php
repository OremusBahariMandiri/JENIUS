<?php

namespace App\Models\Data;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Master\Customer;
use App\Models\Master\Other;
use App\Models\Master\Port;

class JoOther extends Model
{
    use SoftDeletes;

    protected $table = 'b05_jo_other';
    protected $primaryKey = 'id_jo_other';
    public $incrementing = false; // Auto-increment
    protected $keyType = 'int'; // Type integer

    protected $fillable = [
        'id_jo_other',
        'no_jo_other',
        'tgl_jo_other',
        'id_md_cust',
        'id_md_other',
        'id_md_port',
        'id_md_vessel',
        'date_start',
        'date_end',
        'title',
        'note',
        'sts_proses',
    ];

    protected $casts = [
        'tgl_jo_other' => 'date',
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

    public function other()
    {
        return $this->belongsTo(Other::class, 'id_md_other', 'id_md_other');
    }

    public function port()
    {
        return $this->belongsTo(Port::class, 'id_md_port', 'id_md_port');
    }

    public function items()
    {
        return $this->hasMany(JoOtherItem::class, 'id_jo_other', 'id_jo_other');
    }

    public function vessel()
    {
        return $this->belongsTo(Vessel::class, 'id_md_vessel', 'id_md_vessel');
    }
}
