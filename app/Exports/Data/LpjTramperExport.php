<?php

namespace App\Exports\Data;

use Illuminate\Support\Collection;

class LpjTramperExport
{
    protected Collection $lpjTrampers;

    public function __construct(Collection $lpjTrampers)
    {
        $this->lpjTrampers = $lpjTrampers;
    }

    public function toXlsx()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('LPJ Tramper');

        $headers = [
            'No', 'No. LPJ', 'Date', 'JO Number',
            'Kasbon', 'Items', 'Total Kasbon (IDR)', 'Total LPJ (IDR)',
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

        foreach ($this->lpjTrampers as $i => $lpj) {
            $row         = $i + 2;
            $totalKasbon = (float) ($lpj->amount ?? 0);
            $totalLpj    = $lpj->items ? (float) $lpj->items->sum('amount_lpj') : 0;

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $lpj->no_lpj_tram ?? '-');
            $sheet->setCellValue("C{$row}", $lpj->date ? $lpj->date->format('d/m/Y') : '-');
            $sheet->setCellValue("D{$row}", $lpj->joTramper ? $lpj->joTramper->no_jo_tram : '-');
            $sheet->setCellValue("E{$row}", $lpj->kasbons ? $lpj->kasbons->count() : 0);
            $sheet->setCellValue("F{$row}", $lpj->items ? $lpj->items->count() : 0);
            $sheet->setCellValue("G{$row}", $totalKasbon);
            $sheet->setCellValue("H{$row}", $totalLpj);

            $sheet->getStyle("G{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("H{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($borderAll);

            foreach (['A', 'C', 'E', 'F'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            foreach (['G', 'H'] as $c) {
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
            $filename    = 'lpj_tramper_' . date('Ymd_His') . '.xlsx';
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
        $filename = 'lpj_tramper_' . date('Ymd_His') . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No', 'No. LPJ', 'Date', 'JO Number', 'Kasbon', 'Items', 'Total Kasbon (IDR)', 'Total LPJ (IDR)']);

            foreach ($this->lpjTrampers as $i => $lpj) {
                fputcsv($handle, [
                    $i + 1,
                    $lpj->no_lpj_tram ?? '-',
                    $lpj->date ? $lpj->date->format('d/m/Y') : '-',
                    $lpj->joTramper ? $lpj->joTramper->no_jo_tram : '-',
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