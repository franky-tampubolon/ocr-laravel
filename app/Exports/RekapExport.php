<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Font;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapExport implements FromView, WithStyles, WithTitle
{

    protected $data ;
    protected $jumlah_data;
    public function __construct(array $data, int $jumlah_data)
    {
        $this->data = $data;
        $this->jumlah_data = $jumlah_data;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function view() : View
    {
        return view('excel.export', [
            'data' => $this->data,
            'jumlah_data' => $this->jumlah_data
        ]);
    }

    public function styles(Worksheet $sheet)
    {
        $end = $this->jumlah_data+7;
        $sheet->getStyle('A3:K'.$end)->getAlignment()->setWrapText(true);
        $sheet->getRowDimension('3')->setRowHeight(38);
        $sheet->getRowDimension('5')->setRowHeight(8);
        for($i=6; $i<$this->jumlah_data+8; $i++){
            $sheet->getRowDimension($i)->setRowHeight(30);
        }
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
        $sheet->getStyle('A6:K'.$end)->getNumberFormat()->setFormatCode(NumberFormat::FORMAT_NUMBER);
        $sheet->getStyle('A3:K4')->getFont()->setSize(10);
        $sheet->getStyle('A6:K'.$end)->getFont()->setSize(11);
        $sheet->getStyle('A1:A2')->getFont()->setSize(11);
        // $sheet->getStyle('A3:K4')->getFont()->setBold(true);
        $sheet->getStyle('A3:K'.$end)->getAlignment()->setVertical('center');
        $sheet->getStyle('A3:K'.$end)->getAlignment()->setHorizontal('center');
        $sheet->getStyle('A3:K'.$end)->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);
        $sheet->getPageSetup()->setPrintArea('A1:K'.$end);
        $sheet->getPageSetup()->setFitToPage(true);
    }

    public function title(): string
    {

        return date('dmY');
    }

}
