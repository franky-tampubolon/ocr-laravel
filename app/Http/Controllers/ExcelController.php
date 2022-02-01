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
        $jenis = $request->jenis_rekap;
        // dd($jenis);
        if($jenis == 'kebun'){
            $datas = Excel::toArray(new KebunImport, $request->file('excel'));
        }else{
            $datas = Excel::toArray(new RekapImport, $request->file('excel'));
            $jenis = 'UM/PELUNASAN/PPN CPO';
        }
        // dd($request->all());
        
        $new = Arr::except($datas[0], [0]);
        $collections = collect($new)->groupBy([0, 23]);
        // dd($collections);
        $data = [];
        foreach($collections as $key => $collect){
            $values = [];
            foreach($collect as $a => $val){
                $values[] = [
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
        // dd($data);
        $name_file = $request->file('excel')->getClientOriginalName();
        // $name_file = Str::after($name_file, 'TINA_');
        $export = new RekapExport($data, $jenis);
        return Excel::download($export, 'Rekap_'.$name_file.'.xlsx');
    }
}
