<?php

namespace App\Http\Controllers;

use App\Exports\RekapExport;
use App\Imports\KebunImport;
use App\Imports\RekapImport;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    public function index()
    {
        return view('excel.index');
    }

    public function import(Request $request)
    {
        
        $data = [];
        $jenis = $request->jenis_rekap;
        // dd($jenis);
        if($jenis == 'kebun'){
            $datas = Excel::toArray(new KebunImport, $request->file('excel'));
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
                    'jenis_rekap' => $jenis
                ];
            }
        }else{
            $datas = Excel::toArray(new RekapImport, $request->file('excel'));
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
        }  
        // dd($data);
        $name_file = $request->file('excel')->getClientOriginalName();
        // $name_file = Str::after($name_file, 'TINA_');
        $export = new RekapExport($data, $jenis);
        return Excel::download($export, 'Rekap_'.$name_file.'.xlsx');
    }

    public function cek_nominal($amount)
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
