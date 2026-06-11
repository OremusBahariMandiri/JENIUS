<?php

namespace App\Exports\Data;

use Illuminate\Support\Collection;

class JoContractExport
{
    protected Collection $joContracts;

    public function __construct(Collection $joContracts)
    {
        $this->joContracts = $joContracts;
    }

    public function toXlsx()
    {
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet       = $spreadsheet->getActiveSheet();
        $sheet->setTitle('JO Contract');

        $headers = [
            'No',
            'JO Date',
            'JO Number',
            'Contract No',
            'Contract Name',
            'Customer',
            'Period Start',
            'Period End',
            'Area',
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
            $sheet->getStyle("{$cellCol}1")->getAlignment()->setHorizontal(
                \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER
            );
        }

        // Data rows
        foreach ($this->joContracts as $i => $jo) {
            $row         = $i + 2;
            $contract    = $jo->contract;
            $customer    = $contract && $contract->customer ? $contract->customer->customer : '-';
            $dateStart   = $contract && $contract->date_start
                ? \Carbon\Carbon::parse($contract->date_start)->format('d/m/Y') : '-';
            $dateEnd     = $contract && $contract->date_end
                ? \Carbon\Carbon::parse($contract->date_end)->format('d/m/Y') : '-';
            $totalSell   = $jo->items ? $jo->items->sum('hargajual_idr') : 0;

            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $jo->tgl_jo_cont ? $jo->tgl_jo_cont->format('d/m/Y') : '-');
            $sheet->setCellValue("C{$row}", $jo->no_jo_cont ?? '-');
            $sheet->setCellValue("D{$row}", $contract ? $contract->no_contract : '-');
            $sheet->setCellValue("E{$row}", $contract ? $contract->contract : '-');
            $sheet->setCellValue("F{$row}", $customer);
            $sheet->setCellValue("G{$row}", $dateStart);
            $sheet->setCellValue("H{$row}", $dateEnd);
            $sheet->setCellValue("I{$row}", $jo->area ? $jo->area->area : '-');
            $sheet->setCellValue("J{$row}", $jo->title ?? '-');
            $sheet->setCellValue("K{$row}", $jo->items ? $jo->items->count() : 0);
            $sheet->setCellValue("L{$row}", $totalSell);

            // Number format for total
            $sheet->getStyle("L{$row}")->getNumberFormat()
                ->setFormatCode('#,##0.00');

            $sheet->getStyle("A{$row}:{$lastCol}{$row}")->applyFromArray($borderAll);

            // Center align certain columns
            foreach (['A', 'B', 'C', 'G', 'H', 'K'] as $centerCol) {
                $sheet->getStyle("{$centerCol}{$row}")
                    ->getAlignment()
                    ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            }
            // Right align total
            $sheet->getStyle("L{$row}")
                ->getAlignment()
                ->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
        }

        // Auto size columns
        foreach (range('A', $lastCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Freeze header
        $sheet->freezePane('A2');

        return $spreadsheet;
    }

    public function toCsv()
    {
        $filename = 'jo_contract_' . date('Ymd_His') . '.csv';

        return response()->stream(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, [
                'No', 'JO Date', 'JO Number', 'Contract No', 'Contract Name',
                'Customer', 'Period Start', 'Period End', 'Area', 'Title', 'Items', 'Total (IDR)',
            ]);

            foreach ($this->joContracts as $i => $jo) {
                $contract  = $jo->contract;
                $customer  = $contract && $contract->customer ? $contract->customer->customer : '-';
                $dateStart = $contract && $contract->date_start
                    ? \Carbon\Carbon::parse($contract->date_start)->format('d/m/Y') : '-';
                $dateEnd   = $contract && $contract->date_end
                    ? \Carbon\Carbon::parse($contract->date_end)->format('d/m/Y') : '-';
                $totalSell = $jo->items ? $jo->items->sum('hargajual_idr') : 0;

                fputcsv($handle, [
                    $i + 1,
                    $jo->tgl_jo_cont ? $jo->tgl_jo_cont->format('d/m/Y') : '-',
                    $jo->no_jo_cont ?? '-',
                    $contract ? $contract->no_contract : '-',
                    $contract ? $contract->contract : '-',
                    $customer,
                    $dateStart,
                    $dateEnd,
                    $jo->area ? $jo->area->area : '-',
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
            $filename    = 'jo_contract_' . date('Ymd_His') . '.xlsx';
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