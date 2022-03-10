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

class RekapBaruExport implements WithStyles, WithTitle
{
    protected $data;
    protected $jenis;

    public function __construct(array $data, string $jenis)
    {
        $this->data = $data;
        $this->jenis = $jenis;
    }

    public function styles(WorkSheet $sheet)
    {
        $datas = $this->data;
        $row = 1;
        foreach($datas as $data)
        {
            $j = $row+1;
            $sheet->getStyle('A'.$row.':A'.$j)->getFont()->setSize(11);
            $sheet->getCell('A'.$row)->setValue($this->jenis); //baris 1
            $row++; // baris 2
            $sheet->getCell('A'.$row)->setValue(date('d/n/Y'));
            $row++; //baris 3
            // awal header tabel
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':K'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getRowDimension($row)->setRowHeight(38);
            $sheet->getCell('A'.$row)->setValue('NO BTD');
            $sheet->getCell('B'.$row)->setValue('AMOUNT');
            $sheet->getCell('C'.$row)->setValue('DUE DATE');
            $sheet->getCell('D'.$row)->setValue('ADOBE PRINT');
            $sheet->getCell('E'.$row)->setValue('SIGNATURE GROUP A');
            $sheet->getCell('G'.$row)->setValue('SIGNATURE GROUP B');
            $sheet->getCell('J'.$row)->setValue('Diterima I2P Jam');
            $sheet->getCell('K'.$row)->setValue('Paraf PIC I2P');

            $i = $row+1; //baris 4
            $sheet->getStyle('A'.$row.':K'.$i)->getFont()->setSize(10);
            $sheet->mergeCells('A'.$row.':A'.$i);
            $sheet->mergeCells('B'.$row.':B'.$i);
            $sheet->mergeCells('C'.$row.':C'.$i);
            $sheet->mergeCells('D'.$row.':D'.$i);
            $sheet->mergeCells('E'.$row.':F'.$row);
            $sheet->mergeCells('G'.$row.':I'.$row);
            $sheet->mergeCells('J'.$row.':J'.$i);
            $sheet->mergeCells('K'.$row.':K'.$i);
            $row++; //baris 4
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':K'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $sheet->getCell('E'.$row)->setValue('IN');
            $sheet->getCell('F'.$row)->setValue('OUT');
            $sheet->getCell('G'.$row)->setValue('IN QA jam MSIG');
            $sheet->getCell('H'.$row)->setValue('OUT QA jam MSIG');
            $sheet->getCell('I'.$row)->setValue('Received QA MSIG jam');
            // akhir header tabel
            $row++; //baris ke 5
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setWrapText(true);
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':K'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            //baris kecil pertama sebelum data dicetak
            $sheet->getRowDimension($row)->setRowHeight(8);

            // setting lebar dan tinggi dari baris
            $sheet->getColumnDimension('A')->setWidth(15.71);
            $sheet->getColumnDimension('B')->setWidth(15.15);
            $sheet->getColumnDimension('C')->setWidth(10.71);
            $sheet->getColumnDimension('D')->setWidth(8.87);
            $sheet->getColumnDimension('E')->setWidth(8.87);
            $sheet->getColumnDimension('F')->setWidth(8.87);
            $sheet->getColumnDimension('G')->setWidth(8.87);
            $sheet->getColumnDimension('H')->setWidth(8.87);
            $sheet->getColumnDimension('I')->setWidth(8.87);
            $sheet->getColumnDimension('J')->setWidth(8.87);
            $sheet->getColumnDimension('K')->setWidth(8.87);
            $row++; //baris 6
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setWrapText(true);
            $merge = $row + count($data)-1;
            $sheet->mergeCells('D'.$row.':D'.$merge);
            $sheet->mergeCells('E'.$row.':E'.$merge);
            $sheet->mergeCells('F'.$row.':F'.$merge);
            $sheet->mergeCells('G'.$row.':G'.$merge);
            $sheet->mergeCells('H'.$row.':H'.$merge);
            $sheet->mergeCells('I'.$row.':I'.$merge);
            $sheet->mergeCells('J'.$row.':J'.$merge);
            $sheet->mergeCells('K'.$row.':K'.$merge);
            foreach($data as $baris)
            {
                $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setWrapText(true);
                $sheet->getRowDimension($row)->setRowHeight(30); //setting tinggi baris data
                $sheet->getStyle('A'.$row.':K'.$row)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
                $sheet->getStyle('A'.$row.':K'.$row)->getFont()->setSize(11);
                $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setVertical('center');
                $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A'.$row.':K'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
                $sheet->getCell('A'.$row)->setValue($baris['btd_number']);
                $sheet->getCell('B'.$row)->setValue(number_format($baris['amount'],0,',','.'));
                $sheet->getCell('C'.$row)->setValue($baris['due_date']);
                $row++; //cetak baris selanjutnya
            }
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($row)->setRowHeight(30);
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':K'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setWrapText(true);
            $sheet->getRowDimension($row)->setRowHeight(30);
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setVertical('center');
            $sheet->getStyle('A'.$row.':K'.$row)->getAlignment()->setHorizontal('center');
            $sheet->getStyle('A'.$row.':K'.$row)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
            $row++;
            $row++; //baris kosong pemisah dengan tabel selanjutnya

        }
        $sheet->getPageSetup()->setPrintArea('A1:K'.$row);
        $sheet->getPageSetup()->setHorizontalCentered(true);
        $sheet->getPageSetup()->setFitToPage(true);
    }

    public function title(): string
    {

        return date('dnY');
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15.71,
            'B' => 15.15,
            'C' => 10.71,
            'D' => 8.87,
            'E' => 8.87,
            'F' => 8.87,
            'G' => 8.87,
            'H' => 8.87,
            'I' => 8.87,
            'J' => 8.87,
            'K' => 8.87
        ];
    }
}
