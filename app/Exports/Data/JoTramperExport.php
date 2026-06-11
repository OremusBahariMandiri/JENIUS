<?php

namespace App\Exports\Data;

use Illuminate\Support\Collection;

class JoTramperExport
{
    protected Collection $joTrampers;

    public function __construct(Collection $joTrampers)
    {
        $this->joTrampers = $joTrampers;
    }

    public function toXlsx()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('JO Tramper');

        $headers = [
            'No',
            'JO Date',
            'JO Number',
            'Customer',
            'Vessel',
            'Port',
            'Period Start',
            'Period End',
            'Title',
            'Items',
            'Total (IDR)',
        ];

        $borderAll = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color'       => ['rgb' => '000000'],
                ],
            ],
        ];

        $headerFill = [
            'fill' => [
                'fillType'   => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => 'D1FAE5'],
            ],
        ];

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        // Header row
        foreach ($headers as $col => $header) {
            $cellCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue("{$cellCol}1", $header);
            $sheet->getStyle("{$cellCol}1")->getFont()->setBold(true);
            $sheet->getStyle("{$cellCol}1")->applyFromArray($borderAll);
            $sheet->getStyle("{$cellCol}1")->applyFromArray($headerFill);
            $sheet->getStyle("{$cellCol}1")->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        // Data rows
        foreach ($this->joTrampers as $i => $jo) {
            $row       = $i + 2;
            $totalSell = $jo->items ? $jo->items->sum('hargajual_idr') : 0;

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $jo->tgl_jo_tram ? $jo->tgl_jo_tram->format('d/m/Y') : '-');
            $sheet->setCellValue("C{$row}", $jo->no_jo_tram ?? '-');
            $sheet->setCellValue("D{$row}", $jo->customer ? $jo->customer->customer : '-');
            $sheet->setCellValue("E{$row}", $jo->vessel ? $jo->vessel->vessel_name : '-');
            $sheet->setCellValue("F{$row}", $jo->port ? $jo->port->name_port : '-');
            $sheet->setCellValue("G{$row}", $jo->date_start ? $jo->date_start->format('d/m/Y') : '-');
            $sheet->setCellValue("H{$row}", $jo->date_end ? $jo->date_end->format('d/m/Y') : '-');
            $sheet->setCellValue("I{$row}", $jo->title ?? '-');
            $sheet->setCellValue("J{$row}", $jo->items ? $jo->items->count() : 0);
            $sheet->setCellValue("K{$row}", $totalSell);

            $sheet->getStyle("K{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($borderAll);

            foreach (['A', 'B', 'C', 'G', 'H', 'J'] as $centerCol) {
                $sheet->getStyle("{$centerCol}{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            $sheet->getStyle("K{$row}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $sheet->freezePane('A2');

        return $spreadsheet;
    }

    public function toCsv()
    {
        $filename = 'jo_tramper_' . date('Ymd_His') . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'No', 'JO Date', 'JO Number', 'Customer', 'Vessel', 'Port',
                'Period Start', 'Period End', 'Title', 'Items', 'Total (IDR)',
            ]);

            foreach ($this->joTrampers as $i => $jo) {
                $totalSell = $jo->items ? $jo->items->sum('hargajual_idr') : 0;
                fputcsv($handle, [
                    $i + 1,
                    $jo->tgl_jo_tram ? $jo->tgl_jo_tram->format('d/m/Y') : '-',
                    $jo->no_jo_tram ?? '-',
                    $jo->customer ? $jo->customer->customer : '-',
                    $jo->vessel ? $jo->vessel->vessel_name : '-',
                    $jo->port ? $jo->port->name_port : '-',
                    $jo->date_start ? $jo->date_start->format('d/m/Y') : '-',
                    $jo->date_end ? $jo->date_end->format('d/m/Y') : '-',
                    $jo->title ?? '-',
                    $jo->items ? $jo->items->count() : 0,
                    number_format($totalSell, 2, '.', ''),
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
            $filename    = 'jo_tramper_' . date('Ymd_His') . '.xlsx';
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