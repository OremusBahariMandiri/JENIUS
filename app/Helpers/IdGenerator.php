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
     */
    public static function generateDocNo($tableName, $columnName)
    {
        $year   = date('Y');
        $prefix = "JO/{$year}/";

        $tables = [
            'b01_jo_cont'  => 'no_jo_cont',
            'b03_jo_tram'  => 'no_jo_tram',
            'b05_jo_other' => 'no_jo_other',
        ];

        $lastNumber = 0;

        foreach ($tables as $tbl => $col) {
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
                continue;
            }
        }

        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Dokumen Cash Advance (Kasbon) dengan format: CA/[YYYY]/[NNNNN]
     * Sequence increment lintas semua tabel Kasbon
     * Contoh: CA/2026/00001, CA/2026/00002, dst
     */
    public static function generateCaNo($tableName, $columnName)
    {
        $year   = date('Y');
        $prefix = "CA/{$year}/";

        $tables = [
            'c01_kasbon_cont'  => 'id_kasbon_cont',
            'c03_kasbon_tram'  => 'id_kasbon_tram',
            'c05_kasbon_other' => 'id_kasbon_other',
            'c07_kasbon_gen'   => 'id_kasbon_gen',
        ];

        $lastNumber = 0;

        foreach ($tables as $tbl => $col) {
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
                continue;
            }
        }

        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Dokumen LPJ dengan format: LPJ/[YYYY]/[NNNNN]
     * Sequence increment lintas semua tabel LPJ
     * Contoh: LPJ/2026/00001, LPJ/2026/00002, dst
     */
    public static function generateLpjNo($tableName, $columnName)
    {
        $year   = date('Y');
        $prefix = "LPJ/{$year}/";

        $tables = [
            'd01_lpj_cont' => 'no_lpj_cont',
            // tambah tabel LPJ lainnya di sini (tramper, other, dst)
        ];

        $lastNumber = 0;

        foreach ($tables as $tbl => $col) {
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
                continue;
            }
        }

        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Voucher Mutasi Pembayaran dengan format: PV/[YYYY]/[NNNNN]
     * Sequence increment dari tabel e02_mutasi_pembayaran_voucher
     * Contoh: PV/2026/00001, PV/2026/00002, dst
     */
    public static function generateVoucherNo()
    {
        $year   = date('Y');
        $prefix = "PV/{$year}/";

        $tables = [
            'e02_mutasi_pembayaran_voucher' => 'nomor_voucher',
            // tambah tabel voucher lainnya di sini jika ada
        ];

        $lastNumber = 0;

        foreach ($tables as $tbl => $col) {
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
                continue;
            }
        }

        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Voucher — Kas: AK{yy}/{NNNNN}
     * Contoh: AK26/00001
     */
    public static function generateVoucherNoKas()
    {
        $year   = date('y');
        $prefix = "AK{$year}.";

        $last = DB::table('e02_mutasi_pembayaran_voucher')
            ->where('nomor_voucher', 'LIKE', $prefix . '%')
            ->orderByRaw("CAST(RIGHT(nomor_voucher, 5) AS UNSIGNED) DESC")
            ->value('nomor_voucher');

        $newNumber = $last ? ((int) substr($last, -5)) + 1 : 1;
        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Voucher — Bank: BK{yy}/{NNNNN}
     * Contoh: BK26/00001
     */
    public static function generateVoucherNoBank()
    {
        $year   = date('y');
        $prefix = "BK{$year}.";

        $last = DB::table('e02_mutasi_pembayaran_voucher')
            ->where('nomor_voucher', 'LIKE', $prefix . '%')
            ->orderByRaw("CAST(RIGHT(nomor_voucher, 5) AS UNSIGNED) DESC")
            ->value('nomor_voucher');

        $newNumber = $last ? ((int) substr($last, -5)) + 1 : 1;
        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Dokumen Mutasi Pembayaran dengan format: MP/[YYYY]/[NNNNN]
     * Sequence increment lintas semua tabel Mutasi Pembayaran
     * Contoh: MP/2026/00001, MP/2026/00002, dst
     */
    public static function generateMutasiNo()
    {
        $year   = date('Y');
        $prefix = "../{$year}/";

        $tables = [
            'e01_mutasi_pembayaran' => 'nomor',
            // tambah tabel Mutasi lainnya di sini jika ada (tramper, other, dst)
        ];

        $lastNumber = 0;

        foreach ($tables as $tbl => $col) {
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
                continue;
            }
        }

        $newNumber = $lastNumber + 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    public static function generatePenerimaanNo()
    {
        $year   = date('Y');
        $prefix = "MR/{$year}/";

        $last = DB::table('e04_mutasi_penerimaan')
            ->where('nomor', 'LIKE', $prefix . '%')
            ->orderByRaw("CAST(RIGHT(nomor, 5) AS UNSIGNED) DESC")
            ->value('nomor');

        $newNumber = $last ? ((int) substr($last, -5)) + 1 : 1;

        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Voucher Penerimaan — Kas: AM{yy}.{NNNNN}
     * Contoh: AM26.00001
     */
    public static function generateVoucherNoKasPenerimaan()
    {
        $year   = date('y');
        $prefix = "AM{$year}.";

        $last = DB::table('e05_mutasi_penerimaan_voucher')
            ->where('nomor_voucher', 'LIKE', $prefix . '%')
            ->orderByRaw("CAST(RIGHT(nomor_voucher, 5) AS UNSIGNED) DESC")
            ->value('nomor_voucher');

        $newNumber = $last ? ((int) substr($last, -5)) + 1 : 1;
        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Voucher Penerimaan — Bank: BM{yy}.{NNNNN}
     * Contoh: BM26.00001
     */
    public static function generateVoucherNoBankPenerimaan()
    {
        $year   = date('y');
        $prefix = "BM{$year}.";

        $last = DB::table('e05_mutasi_penerimaan_voucher')
            ->where('nomor_voucher', 'LIKE', $prefix . '%')
            ->orderByRaw("CAST(RIGHT(nomor_voucher, 5) AS UNSIGNED) DESC")
            ->value('nomor_voucher');

        $newNumber = $last ? ((int) substr($last, -5)) + 1 : 1;
        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }

    /**
     * Generate Nomor Voucher Penerimaan — Default (tanpa COA): RV/{YYYY}/{NNNNN}
     * Contoh: RV/2026/00001
     */
    public static function generateVoucherNoPenerimaan()
    {
        $year   = date('Y');
        $prefix = "RV/{$year}/";

        $last = DB::table('e05_mutasi_penerimaan_voucher')
            ->where('nomor_voucher', 'LIKE', $prefix . '%')
            ->orderByRaw("CAST(RIGHT(nomor_voucher, 5) AS UNSIGNED) DESC")
            ->value('nomor_voucher');

        $newNumber = $last ? ((int) substr($last, -5)) + 1 : 1;
        return $prefix . str_pad($newNumber, 5, '0', STR_PAD_LEFT);
    }
}