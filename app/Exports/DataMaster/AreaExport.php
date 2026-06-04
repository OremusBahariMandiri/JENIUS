<?php

namespace App\Exports\DataMaster;

use Illuminate\Support\Collection;

class AreaExport
{
    protected Collection $areas;

    public function __construct(Collection $areas)
    {
        $this->areas = $areas;
    }

    public function toXlsx()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Data Area');

        $headers   = ['No', 'Area', 'Note'];
        $borderAll = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ];

        // Header row — bold + border, no fill
        foreach ($headers as $col => $header) {
            $cellCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue("{$cellCol}1", $header);
            $sheet->getStyle("{$cellCol}1")->getFont()->setBold(true);
            $sheet->getStyle("{$cellCol}1")->applyFromArray($borderAll);
        }

        // Data rows
        foreach ($this->areas as $i => $area) {
            $row = $i + 2;
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $area->area ?? '');
            $sheet->setCellValue("C{$row}", $area->note ?? '');

            $sheet->getStyle("A{$row}:C{$row}")->applyFromArray($borderAll);
        }

        foreach (range('A', 'C') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return $spreadsheet;
    }

    public function toCsv()
    {
        $filename = 'areas_' . date('Ymd_His') . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'Area', 'Note']);

            foreach ($this->areas as $i => $area) {
                fputcsv($handle, [
                    $i + 1,
                    $area->area ?? '',
                    $area->note ?? '',
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
            $filename    = 'areas_' . date('Ymd_His') . '.xlsx';
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