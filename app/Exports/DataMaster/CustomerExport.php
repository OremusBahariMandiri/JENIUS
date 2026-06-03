<?php

namespace App\Exports\DataMaster;

use Illuminate\Support\Collection;

class CustomerExport
{
    protected Collection $customers;

    public function __construct(Collection $customers)
    {
        $this->customers = $customers;
    }

    public function toXlsx()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Customer');

        $headers   = ['No', 'Customer', 'Email', 'Phone', 'NPWP', 'Address', 'Website', 'Note'];
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
        foreach ($this->customers as $i => $customer) {
            $row = $i + 2;
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $customer->customer ?? '');
            $sheet->setCellValue("C{$row}", $customer->email    ?? '');
            $sheet->setCellValue("D{$row}", $customer->phone    ?? '');
            $sheet->setCellValue("E{$row}", $customer->npwp     ?? '');
            $sheet->setCellValue("F{$row}", $customer->address  ?? '');
            $sheet->setCellValue("G{$row}", $customer->website  ?? '');
            $sheet->setCellValue("H{$row}", $customer->note     ?? '');

            $sheet->getStyle("A{$row}:H{$row}")->applyFromArray($borderAll);
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    public function toCsv()
    {
        $filename = 'customers_' . date('Ymd_His') . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Customer', 'Email', 'Phone', 'NPWP', 'Address', 'Website', 'Note']);

            foreach ($this->customers as $i => $customer) {
                fputcsv($handle, [
                    $i + 1,
                    $customer->customer ?? '',
                    $customer->email    ?? '',
                    $customer->phone    ?? '',
                    $customer->npwp     ?? '',
                    $customer->address  ?? '',
                    $customer->website  ?? '',
                    $customer->note     ?? '',
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
            $filename    = 'customers_' . date('Ymd_His') . '.xlsx';
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