<?php

namespace App\Exports;

use Carbon\Carbon;
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
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Reader\Xml\Style\Fill;

class UpstreamVendorExport implements WithTitle, WithStyles, WithColumnWidths
{
    protected $data;
    protected $name_file;

    public function __construct(array $data, string $name_file)
    {
        $this->data = $data;
        $this->name_file = $name_file;
    }

    public function styles(WorkSheet $sheet)
    {
        $data = $this->data;
        $row = 1;
        foreach($data as $warna => $value)
        {
            $sheet->getCell('A'.$row)->setValue('nama file : ');
            $sheet->getStyle('A'.$row)->getFont()->setSize(10);
            $sheet->getCell('B'.$row)->setValue($this->name_file);
            $sheet->getStyle('B'.$row)->getFont()->setSize(10);
            $row++;
            $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setName('Comic Sans MS');
            $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setSize(20);
            $sheet->getStyle('A'.$row.':E'.$row)->getFill()->applyFromArray(['fillType' => 'solid', 'color' => ['rgb' => 'D9D9D9']]);
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($row)->setRowHeight(60);
            $sheet->getCell('A'.$row)->setValue('TANGGAL');
            $sheet->getCell('B'.$row)->setValue('PSM');
            $sheet->getCell('C'.$row)->setValue('NOMOR MAP');
            $sheet->getCell('D'.$row)->setValue('NOMOR BTD');
            $sheet->getCell('E'.$row)->setValue('NOMINAL');

            $row++; //baris ke 3
            $sheet->getCell('A'.$row)->setValue(Carbon::now()->isoFormat('D MMMM Y'));
            $merge = $row + count($value)-1;
            $sheet->mergeCells('A'.$row.':A'.$merge);
            $sheet->mergeCells('B'.$row.':B'.$merge);
            $sheet->mergeCells('C'.$row.':C'.$merge);
            foreach($value as $nominal => $b){

                $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setSize(18);
                $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setName('Comic Sans MS');
                $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);
                $sheet->getStyle('A'.$row.':B'.$row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                $sheet->getStyle('D'.$row.':E'.$row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setVertical('center');
                $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A'.$row.':E'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getRowDimension($row)->setRowHeight(45);
                $sheet->getCell('B'.$row)->setValue(Str::upper($b['psm']));
                $sheet->getCell('D'.$row)->setValue($b['no_btd']);
                $sheet->setCellValueExplicit('E'.$row, $b['amount'], DataType::TYPE_STRING);
                $row++; //cetak baris selanjutnya
            }
            $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setSize(12);
            $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setName('Comic Sans MS');
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($row)->setRowHeight(45);
            $row++;
            $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setSize(12);
            $sheet->getStyle('A'.$row.':E'.$row)->getFont()->setName('Comic Sans MS');
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':E'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($row)->setRowHeight(45);
            $row++;
            $row++;
        }
        $sheet->getPageSetup()->setPrintArea('A1:E'.$row);
        $sheet->getPageSetup()->setFitToPage(true);
    }

    public function columnWidths(): array
    {
        return [
            'A' => 28,
            'B' => 28,
            'C' => 29,
            'D' => 32,
            'E' => 22
        ];
    }

    public function title(): string
    {
        return date('dnY');
    }
}
