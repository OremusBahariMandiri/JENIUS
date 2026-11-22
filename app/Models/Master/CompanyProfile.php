<?php

namespace App\Models\Master;

use Illuminate\Database\Eloquent\Model;

class CompanyProfile extends Model
{
    protected $table      = 'a15_company_profile';
    protected $primaryKey = 'id_company_profile';

    protected $fillable = [
        'nama_perusahaan',
        'nama_direktur',
        'jabatan',
        'alamat',
        'nama_ttd_kwitansi',
        'jabatan_ttd_kwitansi',
    ];
}
