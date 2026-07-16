<?php

namespace App\Exports\Data;

use Illuminate\Support\Collection;

class KasbonGenExport
{
    protected Collection $kasbonGens;

    public function __construct(Collection $kasbonGens)
    {
        $this->kasbonGens = $kasbonGens;
    }

    public function toXlsx()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Kasbon General');

        // KasbonGen tidak punya JO & HPP, hanya CA Amount
        $headers = [
            'No', 'CA No', 'CA Date',
            'Departemen', 'Branch', 'Release To', 'Release Date',
            'Items', 'Total CA (IDR)',
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

        foreach ($this->kasbonGens as $i => $kasbon) {
            $row     = $i + 2;
            $totalCA = $kasbon->items ? $kasbon->items->sum('nilai_kasbon') : 0;

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $kasbon->id_kasbon_gen ?? '-');
            $sheet->setCellValue("C{$row}", $kasbon->tgl_kasbon ? $kasbon->tgl_kasbon->format('d/m/Y') : '-');
            $sheet->setCellValue("D{$row}", $kasbon->departemen ? $kasbon->departemen->nama_dep : '-');
            $sheet->setCellValue("E{$row}", $kasbon->cabang ? $kasbon->cabang->nama_branch : '-');
            $sheet->setCellValue("F{$row}", $kasbon->release ? $kasbon->release->nama_release : '-');
            $sheet->setCellValue("G{$row}", $kasbon->tgl_release ? $kasbon->tgl_release->format('d/m/Y') : '-');
            $sheet->setCellValue("H{$row}", $kasbon->items ? $kasbon->items->count() : 0);
            $sheet->setCellValue("I{$row}", $totalCA);

            $sheet->getStyle("I{$row}")->getNumberFormat()->setFormatCode('#,##0.00');
            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($borderAll);

            foreach (['A', 'C', 'G', 'H'] as $c) {
                $sheet->getStyle("{$c}{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            $sheet->getStyle("I{$row}")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
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
            $filename    = 'kasbon_general_' . date('Ymd_His') . '.xlsx';
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
        $filename = 'kasbon_general_' . date('Ymd_His') . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['No','CA No','CA Date','Departemen','Branch','Release To','Release Date','Items','Total CA (IDR)']);

            foreach ($this->kasbonGens as $i => $kasbon) {
                fputcsv($handle, [
                    $i + 1,
                    $kasbon->id_kasbon_gen ?? '-',
                    $kasbon->tgl_kasbon ? $kasbon->tgl_kasbon->format('d/m/Y') : '-',
                    $kasbon->departemen ? $kasbon->departemen->nama_dep : '-',
                    $kasbon->cabang ? $kasbon->cabang->nama_branch : '-',
                    $kasbon->release ? $kasbon->release->nama_release : '-',
                    $kasbon->tgl_release ? $kasbon->tgl_release->format('d/m/Y') : '-',
                    $kasbon->items ? $kasbon->items->count() : 0,
                    number_format($kasbon->items ? $kasbon->items->sum('nilai_kasbon') : 0, 2, '.', ''),
                ]);
            }
            fclose($handle);
        }, 200, ['Content-Type' => 'text/csv', 'Content-Disposition' => "attachment; filename=\"{$filename}\""]);
    }
}