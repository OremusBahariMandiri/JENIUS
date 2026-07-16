<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class Departemen extends Model
{
    protected $table = 'a08_md_dep';
    protected $primaryKey = 'id_md_dep';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id_md_dep',
        'skt_dep',
        'nama_dep',
        'keterangan',
    ];
}