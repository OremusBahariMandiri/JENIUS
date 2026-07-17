<?php

namespace App\Exports\DataMaster;

use Illuminate\Support\Collection;

class InvoiceExport
{
    protected Collection $invoices;

    public function __construct(Collection $invoices)
    {
        $this->invoices = $invoices;
    }

    public function toXlsx()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Item');

        $headers   = ['No', 'Category', 'Item', 'Jo Category', 'Note'];
        $borderAll = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ];

        foreach ($headers as $col => $header) {
            $cellCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue("{$cellCol}1", $header);
            $sheet->getStyle("{$cellCol}1")->getFont()->setBold(true);
            $sheet->getStyle("{$cellCol}1")->applyFromArray($borderAll);
        }

        foreach ($this->invoices as $i => $invoice) {
            $row = $i + 2;
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $invoice->invoice_ctg  ?? '');
            $sheet->setCellValue("C{$row}", $invoice->invoice_typ  ?? '');
            $sheet->setCellValue("D{$row}", $invoice->jo_ctg_label ?? $invoice->jo_ctg ?? '');
            $sheet->setCellValue("E{$row}", $invoice->note         ?? '');

            $sheet->getStyle("A{$row}:E{$row}")->applyFromArray($borderAll);
        }

        foreach (range('A', 'E') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    public function toCsv()
    {
        $filename = 'invoices_' . date('Ymd_His') . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Category', 'Item', 'Jo Category', 'Note']);

            foreach ($this->invoices as $i => $invoice) {
                fputcsv($handle, [
                    $i + 1,
                    $invoice->invoice_ctg  ?? '',
                    $invoice->invoice_typ  ?? '',
                    $invoice->jo_ctg_label ?? $invoice->jo_ctg ?? '',
                    $invoice->note         ?? '',
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
            $filename    = 'invoices_' . date('Ymd_His') . '.xlsx';
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