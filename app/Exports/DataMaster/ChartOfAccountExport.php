<?php

namespace App\Exports\DataMaster;

use Illuminate\Support\Collection;

class ChartOfAccountExport
{
    protected Collection $accounts;

    public function __construct(Collection $accounts)
    {
        $this->accounts = $accounts;
    }

    public function toXlsx()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Chart of Account');

        $headers = [
            'No',
            'No. Account',
            'Account Name',
            'Parent Account',
            'Cost Type',
            'Type',
            'Payment Type',
            'Opening Balance',
            'Current Balance',
        ];

        $borderAll = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ];

        // ── Header row ──────────────────────────────────────────────────────────
        foreach ($headers as $col => $header) {
            $cellCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $cell    = "{$cellCol}1";

            $sheet->setCellValue($cell, $header);
            $sheet->getStyle($cell)->getFont()->setBold(true);
            $sheet->getStyle($cell)->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($cell)->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('D1FAE5'); // hijau muda — sesuai tema
            $sheet->getStyle($cell)->applyFromArray($borderAll);
        }

        // ── Data rows ────────────────────────────────────────────────────────────
        foreach ($this->accounts as $i => $acc) {
            $row    = $i + 2;
            $parent = $acc->parentAccount
                ? $acc->parentAccount->kode_perkiraan . ' - ' . $acc->parentAccount->nama
                : '-';

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $acc->no_account    ?? '-');
            $sheet->setCellValue("C{$row}", $acc->account_name);
            $sheet->setCellValue("D{$row}", $parent);
            $sheet->setCellValue("E{$row}", $acc->parentAccount?->costType?->name ?? '-');
            $sheet->setCellValue("F{$row}", $acc->type          ?? '-');
            $sheet->setCellValue("G{$row}", $acc->payment_type  ?? '-');
            $sheet->setCellValue("H{$row}", (float) $acc->opening_balance);
            $sheet->setCellValue("I{$row}", (float) $acc->current_balance);

            // Number format untuk balance
            $sheet->getStyle("H{$row}")->getNumberFormat()
                ->setFormatCode('#,##0.00');
            $sheet->getStyle("I{$row}")->getNumberFormat()
                ->setFormatCode('#,##0.00');

            // Right-align balance columns
            $sheet->getStyle("H{$row}:I{$row}")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

            // Center nomor urut
            $sheet->getStyle("A{$row}")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($borderAll);
        }

        // ── Column widths ────────────────────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(6);   // No
        $sheet->getColumnDimension('B')->setWidth(14);  // No. Account
        $sheet->getColumnDimension('C')->setWidth(30);  // Account Name
        $sheet->getColumnDimension('D')->setWidth(35);  // Parent Account
        $sheet->getColumnDimension('E')->setWidth(20);  // Cost Type
        $sheet->getColumnDimension('F')->setWidth(12);  // Type
        $sheet->getColumnDimension('G')->setWidth(14);  // Payment Type
        $sheet->getColumnDimension('H')->setWidth(18);  // Opening Balance
        $sheet->getColumnDimension('I')->setWidth(18);  // Current Balance

        return $spreadsheet;
    }

    public function toCsv()
    {
        $filename = 'chart_of_account_' . date('Ymd_His') . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'No', 'No. Account', 'Account Name', 'Parent Account',
                'Cost Type', 'Type', 'Payment Type', 'Opening Balance', 'Current Balance',
            ]);

            foreach ($this->accounts as $i => $acc) {
                $parent = $acc->parentAccount
                    ? $acc->parentAccount->kode_perkiraan . ' - ' . $acc->parentAccount->nama
                    : '-';

                fputcsv($handle, [
                    $i + 1,
                    $acc->no_account   ?? '-',
                    $acc->account_name,
                    $parent,
                    $acc->parentAccount?->costType?->name ?? '-',
                    $acc->type         ?? '-',
                    $acc->payment_type ?? '-',
                    $acc->opening_balance,
                    $acc->current_balance,
                ]);
            }

            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }

    public function download()
    {
        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $spreadsheet = $this->toXlsx();
            $filename    = 'chart_of_account_' . date('Ymd_His') . '.xlsx';
            $writer      = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

            ob_start();
            $writer->save('php://output');
            $content = ob_get_clean();

            return response($content, 200, [
                'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            ]);
        }

        return $this->toCsv();
    }
}
