<?php

namespace App\Exports;

use Illuminate\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;


class CashflowExport implements FromView, WithHeadings, WithMapping, WithStyles
{
    public $cashflows, $startDate, $endDate;

    public function __construct($cashflows, $startDate, $endDate)
    {
        $this->cashflows = $cashflows;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function headings(): array
{
    return ['Tanggal', 'Tipe', 'Keterangan', 'Jumlah'];
}

public function map($row): array
{
    return [
        \Carbon\Carbon::parse($row->tanggal)->format('d M Y'),
        $row->tipe,
        $row->keterangan,
        $row->jumlah,
    ];
}

public function styles(Worksheet $sheet)
{
    $rowIndex = 2; // karena row 1 = headings

    foreach ($this->cashflows as $row) {
        $color = $row->tipe === 'Pemasukan' ? '00FF00' : 'FF0000'; // green or red
        $sheet->getStyle("A{$rowIndex}:D{$rowIndex}")->getFill()->applyFromArray([
            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
            'startColor' => ['rgb' => $color],
        ]);
        $rowIndex++;
    }

    return [
        // Optional: styling header
        1 => ['font' => ['bold' => true]],
    ];
}


    public function view(): View
    {
        return view('exports.cashflow', [
            'cashflows' => $this->cashflows,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate
        ]);
    }
}
