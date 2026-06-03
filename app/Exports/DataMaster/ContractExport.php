<?php

namespace App\Exports\DataMaster;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class ContractExport
{
    protected Collection $contracts;

    public function __construct(Collection $contracts)
    {
        $this->contracts = $contracts;
    }

    public function toXlsx()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Contract');

        $headers   = ['No', 'No. Contract', 'Contract Name', 'Customer', 'Expenditure', 'Date Start', 'Date End', 'Status', 'Note'];
        $borderAll = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ];

        // Header row
        foreach ($headers as $col => $header) {
            $cellCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue("{$cellCol}1", $header);
            $sheet->getStyle("{$cellCol}1")->getFont()->setBold(true);
            $sheet->getStyle("{$cellCol}1")->applyFromArray($borderAll);
        }

        // Data rows
        foreach ($this->contracts as $i => $contract) {
            $row    = $i + 2;
            $status = $contract->date_end >= Carbon::today() ? 'Active' : 'Expired';

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $contract->no_contract          ?? '');
            $sheet->setCellValue("C{$row}", $contract->contract             ?? '');
            $sheet->setCellValue("D{$row}", $contract->customer->customer   ?? '');
            $sheet->setCellValue("E{$row}", $contract->expenditure          ?? 0);
            $sheet->setCellValue("F{$row}", $contract->date_start ? $contract->date_start->format('d/m/Y') : '');
            $sheet->setCellValue("G{$row}", $contract->date_end   ? $contract->date_end->format('d/m/Y')   : '');
            $sheet->setCellValue("H{$row}", $status);
            $sheet->setCellValue("I{$row}", $contract->note                 ?? '');

            $sheet->getStyle("E{$row}")
                  ->getNumberFormat()
                  ->setFormatCode('#,##0.00');

            $sheet->getStyle("A{$row}:I{$row}")->applyFromArray($borderAll);
        }

        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    public function toCsv()
    {
        $filename = 'contracts_' . date('Ymd_His') . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'No. Contract', 'Contract Name', 'Customer', 'Expenditure', 'Date Start', 'Date End', 'Status', 'Note']);

            foreach ($this->contracts as $i => $contract) {
                $status = $contract->date_end >= Carbon::today() ? 'Active' : 'Expired';
                fputcsv($handle, [
                    $i + 1,
                    $contract->no_contract          ?? '',
                    $contract->contract             ?? '',
                    $contract->customer->customer   ?? '',
                    $contract->expenditure          ?? 0,
                    $contract->date_start ? $contract->date_start->format('d/m/Y') : '',
                    $contract->date_end   ? $contract->date_end->format('d/m/Y')   : '',
                    $status,
                    $contract->note                 ?? '',
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
            $filename    = 'contracts_' . date('Ymd_His') . '.xlsx';
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