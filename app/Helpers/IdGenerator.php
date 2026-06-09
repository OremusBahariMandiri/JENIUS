<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

class IdGenerator
{
    /**
     * Generate ID dengan format: [KODE_TABEL][MM][YY][NNNNN]
     * Contoh: B03052600001
     */
    public static function generate($tableCode, $tableName, $columnName)
    {
        $month  = date('m');
        $year   = date('y');
        $prefix = $tableCode . $month . $year;

        $lastId = DB::table($tableName)
            ->where($columnName, 'LIKE', $prefix . '%')
            ->orderByRaw("CAST(RIGHT({$columnName}, 5) AS UNSIGNED) DESC")
            ->value($columnName);

        $newNumber = $lastId ? ((int) substr($lastId, -5)) + 1 : 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Dokumen JO Global dengan format: JO/[YYYY]/[NNNNN]
     * Sequence increment lintas semua tabel JO (tramper, other, dst)
     * Contoh: JO/2026/00001, JO/2026/00002, dst
     *
     * @param string $tableName  Nama tabel untuk menyimpan nomor
     * @param string $columnName Nama kolom nomor dokumen
     * @return string
     */
    public static function generateDocNo($tableName, $columnName)
    {
        $year   = date('Y');
        $prefix = "JO/{$year}/";

        // Cari nomor terakhir dari SEMUA tabel JO
        $tables = [
            'b01_jo_cont'  => 'no_jo_cont',
            'b03_jo_tram'  => 'no_jo_tram',
            'b05_jo_other' => 'no_jo_other',
            // tambah tabel JO lainnya di sini
        ];

        $lastNumber = 0;

        foreach ($tables as $tbl => $col) {
            // Cek apakah kolom ada di tabel (untuk antisipasi migration belum jalan)
            try {
                $last = DB::table($tbl)
                    ->where($col, 'LIKE', $prefix . '%')
                    ->orderByRaw("CAST(RIGHT({$col}, 5) AS UNSIGNED) DESC")
                    ->value($col);

                if ($last) {
                    $num = (int) substr($last, -5);
                    if ($num > $lastNumber) {
                        $lastNumber = $num;
                    }
                }
            } catch (\Exception $e) {
                // skip jika tabel/kolom belum ada
                continue;
            }
        }

        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}
