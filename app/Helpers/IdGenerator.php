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
        // Get current month and year
        $month = date('m'); // 05
        $year = date('y');  // 26 (2 digit terakhir)

        // Prefix: B02 + 05 + 26 = B020526
        $prefix = $tableCode . $month . $year;

        // Get last ID with same prefix
        $lastId = DB::table($tableName)
            ->where($columnName, 'LIKE', $prefix . '%')
            ->orderBy($columnName, 'desc')
            ->value($columnName);

        if ($lastId) {
            // Extract nomor urut dari ID terakhir (5 digit terakhir)
            $lastNumber = (int) substr($lastId, -5);
            $newNumber = $lastNumber + 1;
        } else {
            // Jika belum ada, mulai dari 1
            $newNumber = 1;
        }

        // Format: B020526 + 00001 (5 digit dengan leading zeros)
        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}