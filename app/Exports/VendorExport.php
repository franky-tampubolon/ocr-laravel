<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Border;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\WithColumnWidths;

class VendorExport implements WithTitle, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function styles(WorkSheet $sheet)
    {
        $data = $this->data;
        $row = 1;
        foreach($data as $warna => $value)
        {
            
            $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setSize(12);
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);                
            $sheet->getStyle('A'.$row.':E'.$row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($row)->setRowHeight(32);
            $sheet->getCell('A'.$row)->setValue('No Map');
            $sheet->getCell('B'.$row)->setValue('No BTD');
            $sheet->getCell('C'.$row)->setValue('Nominal');
            $sheet->getCell('D'.$row)->setValue('PCA');
            $sheet->getCell('E'.$row)->setValue('WARNA MAP');

            $row++; //baris ke 2
            foreach($value as $nominal => $b){
                $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setSize(12);
                $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);
                $sheet->getStyle('A'.$row.':E'.$row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setVertical('center');
                $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A'.$row.':E'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getRowDimension($row)->setRowHeight(32);
                $sheet->getCell('B'.$row)->setValue($b['no_btd']);
                $sheet->getCell('C'.$row)->setValue(number_format($b['amount']));
                $sheet->getCell('D'.$row)->setValue(Str::upper($b['pca']));
                $sheet->getCell('E'.$row)->setValue(Str::upper($b['warna_map']));
                $row++; //cetak baris selanjutnya
            }
            $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setSize(12);
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($row)->setRowHeight(32);
            $row++;
            $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setSize(12);
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($row)->setRowHeight(32);
            $row++;
            
        }
        $sheet->getPageSetup()->setPrintArea('A1:E'.$row);
        $sheet->getPageSetup()->setFitToPage(true);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 11,
            'B' => 20.86,
            'C' => 19.29,
            'D' => 26.43,
            'E' => 14          
        ];
    }

    public function title(): string
    {
        return date('dnY');
    }
}
