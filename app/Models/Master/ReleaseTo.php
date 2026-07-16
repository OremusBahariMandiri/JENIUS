<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class ReleaseTo extends Model
{
    protected $table = 'a10_md_release_to';
    protected $primaryKey = 'id_md_release';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_md_release',
        'nama_release',
        'tujuan',
        'rekening',
        'no_telepon',
        'alamat',
        'note',
    ];
}