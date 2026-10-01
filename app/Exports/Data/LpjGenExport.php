<?php

namespace App\Exports\Data;

use Illuminate\Support\Collection;

class LpjGenExport
{
    protected Collection $lpjGens;

    public function __construct(Collection $lpjGens)
    {
        $this->lpjGens = $lpjGens;
    }

    public function toXlsx()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('LPJ General');

        $headers = [
            'No', 'No. LPJ', 'Date',
            'Kasbon', 'Items', 'Total CA (IDR)', 'Total LPJ (IDR)',
        ];

        $borderAll  = ['borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']]]];
        $headerFill = ['fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D1FAE5']]];
        $lastCol    = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($headers));

        foreach ($headers as $col => $header) {
            $cellCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col + 1);
            $sheet->setCellValue("{$cellCol}1", $header);
            $sheet->getStyle("{$cellCol}1")->getFont()->setBold(true);
            $sheet->getStyle("{$cellCol}1")->applyFromArray($borderAll);
            $sheet->getStyle("{$cellCol}1")->applyFromArray($headerFill);
            $sheet->getStyle("{$cellCol}1")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        }

        foreach ($this->lpjGens as $i => $lpj) {
            $row         = $i + 2;
            $totalKasbon = (float) ($lpj->amount ?? 0);
            $totalLpj    = $lpj->items ? (float) $lpj->items->sum('amount_lpj') : 0;

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $lpj->no_lpj_gen ?? '-');
            $sheet->setCellValue("C{$row}", $lpj->date ? $lpj->date->format('d/m/Y') : '-');
            $sheet->setCellValue("D{$row}", $lpj->kasbons ? $lpj->kasbons->count() : 0);
            $sheet->setCellValue("E{$row}", $lpj->items ? $lpj->items->count() : 0);
            $sheet->setCellValue("F{$row}", $totalKasbon);
            $sheet->setCellValue("G{$row}", $totalLpj);

            $sheet->getStyle("F{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($borderAll);

            foreach (['A', 'C', 'D', 'E'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            foreach (['F', 'G'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
            }
        }

        foreach (range(1, count($headers)) as $col) {
            $sheet->getColumnDimension(\PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col))->setAutoSize(true);
        }
        $sheet->freezePane('A2');

        return $spreadsheet;
    }

    public function download()
    {
        if (class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
            $spreadsheet = $this->toXlsx();
            $filename    = 'lpj_general_' . date('Ymd_His') . '.xlsx';
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

    public function toCsv()
    {
        $filename = 'lpj_general_' . date('Ymd_His') . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'No. LPJ', 'Date', 'Kasbon', 'Items', 'Total CA (IDR)', 'Total LPJ (IDR)']);

            foreach ($this->lpjGens as $i => $lpj) {
                fputcsv($handle, [
                    $i + 1,
                    $lpj->no_lpj_gen ?? '-',
                    $lpj->date ? $lpj->date->format('d/m/Y') : '-',
                    $lpj->kasbons ? $lpj->kasbons->count() : 0,
                    $lpj->items ? $lpj->items->count() : 0,
                    number_format($lpj->amount ?? 0, 2, '.', ''),
                    number_format($lpj->items ? $lpj->items->sum('amount_lpj') : 0, 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}