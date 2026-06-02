<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class IdGenerator
{
    /**
     * Generate ID dengan format: [KODE_TABEL][MM][YY][NNNNN]
     * Contoh: B02052600001
     *
     * @param string $tableCode Kode tabel (contoh: B02)
     * @param string $tableName Nama tabel untuk query
     * @param string $columnName Nama kolom ID
     * @return string
     */
    public static function generate($tableCode, $tableName, $columnName)
    {
        $month  = date('m');
        $year   = date('y');
        $prefix = $tableCode . $month . $year;

        // Cast 5 digit terakhir ke unsigned integer agar sort numerik, bukan string
        $lastId = DB::table($tableName)
            ->where($columnName, 'LIKE', $prefix . '%')
            ->orderByRaw("CAST(RIGHT({$columnName}, 5) AS UNSIGNED) DESC")
            ->value($columnName);

        $newNumber = $lastId ? ((int) substr($lastId, -5)) + 1 : 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}