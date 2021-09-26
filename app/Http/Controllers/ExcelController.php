<?php

namespace App\Http\Controllers;

use App\Exports\RekapExport;
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
        // dd($request->all());
        $datas = Excel::toArray(new RekapImport, $request->file('excel'));
        $new = Arr::except($datas[0], [0]);
        $collections = collect($new)->groupBy([23]);
        $data = [];
        foreach($collections as $key => $collect){
            $data[] = [
                'btd_number' => $key,
                'amount' => (int) Str::after($collect[count($collect)-1][10], '-'),
                'due_date' => Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($collect[0][8]))->format('d/n/Y')
            ];
        }

        $name_file = $request->file('excel')->getClientOriginalName();
        $name_file = Str::after($name_file, 'TINA_');
        $export = new RekapExport($data, count($data));
        return Excel::download($export, 'Rekap_'.$name_file.'.xlsx');
        // dd($data, count($data));
        // return view('excel.tampil', ['data' => $data, 'jumlah_data'=>count($data)]);
    }
}
