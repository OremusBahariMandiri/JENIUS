<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
{
    protected $table = 'a09_md_branch';
    protected $primaryKey = 'id_md_branch';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_md_branch',
        'skt_branch',
        'nama_branch',
        'area',
        'alamat',
        'no_telepon',
        'email',
        'note',
    ];
}