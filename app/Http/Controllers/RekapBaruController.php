<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\RekapBaruExport;
use App\Imports\KebunImport;
use App\Imports\RekapImport;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

class RekapBaruController extends Controller
{
    public function import(Request $request)
    {
        
        $jenis = $request->jenis_rekap;
        if($jenis === 'kebun')
        {
            $data = $this->import_kebun($request->file('excel'));
        }else if($jenis === 'cpo')
        {
            $data = $this->import_cpo($request->file('excel'));
            $jenis = 'UM/PELUNASAN/PPN CPO';
        }else{
            $data = $this->import_vendor($request->file('excel'));
            $jenis = 'Vendor';
        }
        // dd($data);
        $new_data = [];
        foreach($data as $values)
        {
            foreach($values['data'] as $value)
            {
                $new_data[] = $value;
            }
        }
        // dd($new_data);
        $name_file = $request->file('excel')->getClientOriginalName();
        return $this->export_excel($new_data, $jenis, $name_file);

    }

    protected function import_kebun($file)
    {
        $data = [];
        $datas = Excel::toArray(new KebunImport, $file);
        $new = Arr::except($datas[0], [0]);
        $collections = collect($new)->groupBy([0, 23]);
        foreach($collections as $key => $collect){
            $values = [];
            foreach($collect as $a => $val){
                $amount = (int) Str::after($val[count($val)-1][10], '-');
                $amount = $this->cek_nominal($amount);
                // dd($amount);
                $values[$amount][] = [
                    'btd_number' => $a,
                    'amount' => (int) Str::after($val[count($val)-1][10], '-'),
                    'due_date' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($val[0][8]))->format('d/n/Y')
                ];
            }
            $data[] = [
                'company_code' => $key,
                'data' => $values,
                'jumlah_data' => count($values),
                'jenis_rekap' => 'kebun'
            ];
        }
        return $data;
    }

    protected function import_cpo($file)
    {
        $data = [];
        $datas = Excel::toArray(new RekapImport, $file);
        $new = Arr::except($datas[0], [0]);
        $collections = collect($new)->groupBy([23]);
        $jenis = 'UM/PELUNASAN/PPN CPO';
        $values = [];
        foreach($collections as $key => $collect){
            $amount = (int) Str::after($collect[count($collect)-1][10], '-');
            $amount = $this->cek_nominal($amount);
            // dd($amount);
            $values[$amount][] = [
                'btd_number' => $key,
                'amount' => (int) Str::after($collect[count($collect)-1][10], '-'),
                'due_date' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($collect[0][8]))->format('d/n/Y'),
            ];
        }
        $data[] = [
            'company_code' => 0,
            'data' => $values,
            'jumlah_data' => count($values),
            'jenis_rekap' => $jenis
        ];
        return $data;
    }

    protected function export_excel($data, $jenis, $name_file)
    {
        // $name_file = Str::after($name_file, 'TINA_');
        $export = new RekapBaruExport($data, $jenis);
        return Excel::download($export, 'Rekap_'.$name_file.'.xlsx');
    }

    protected function cek_nominal($amount)
    {
        if($amount <200000000 && $amount >0){
            return 'kurang 200 juta';
        }else if($amount < 1000000000){
            return '200 juta - 1 milyar';
        }else{
            return 'diatas 1 milyar';
        }
    }
}
