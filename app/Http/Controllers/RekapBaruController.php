<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exports\RekapBaruExport;
use App\Exports\VendorExport;
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
            $name_file = $request->file('excel')->getClientOriginalName();
            $export = new VendorExport($data, $name_file);
            return Excel::download($export, 'Rekap_'.$name_file.'.xlsx');
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

    protected function import_vendor($file)
    {
        $data = [];
        $datas = Excel::toArray(new RekapImport, $file);
        $new = Arr::except($datas[0], [0]);
        $collections = collect($new)->groupBy([23]);
        // dd($collections);
        foreach($collections as $key => $row)
        {
            // cek HO atau tidak
            $ho = explode(',', $row[0][21])[0];
            if(strtoupper($ho) === 'HO'){
                // echo $ho;
                $map = 'Biru';
            }else{
                $due_date = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0][8]));
                $map = $this->cek_map($due_date);
                // dd($map);
            }

            // cek PCA index 1
            $company_code = (int) $row[0][0];
            $pca = $row[0][1];
            $pca = $this->cek_pca($pca, $company_code);

            // cek nominal
            $amount = (int) Str::after($row[count($row)-1][10], '-');
            $amount = $this->cek_nominal($amount);
            if($amount === 'kurang 200 juta'){
                // cek apakah PO atau tidak melalui Purchasing Document
                if($row[0][6]){
                    $jenis_po = 'PO';
                }else{
                    $jenis_po = 'Non PO';
                }
                $data[$map][$amount][$jenis_po][] = [
                    'warna_map' => $map,
                    'no_btd' => $key,
                    'amount' => (int) Str::after($row[count($row)-1][10], '-'),
                    'pca' => $pca .' - '.$jenis_po
                ];
            }else{
                $data[$map][$amount][] = [
                    'warna_map' => $map,
                    'no_btd' => $key,
                    'amount' => (int) Str::after($row[count($row)-1][10], '-'),
                    'pca' => $pca
                ];
            }
        }
        // dd($data);
        $new_data = [];
        foreach($data as $warna => $a)
        {
            // dd($a);
            foreach($a as $nominal => $b)
            {
                
                if($nominal === 'kurang 200 juta'){
                    foreach($b as $po => $c)
                    {
                        $new_data[] = $c;
                    }
                }else{
                    $new_data[] = $b;
                }
                
            }
        }
        // dd($new_data);
        return $new_data;
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

    protected function cek_map($due_date)
    {
        $now = Carbon::now();
        if($due_date ->lessThanOrEqualTo($now->addDays(1))){
            return 'Kuning' ; //bayar besok
        }else if($due_date->greaterThanOrEqualTo($now->addDays(2))){
            return 'Hijau'; //bayar lusa ke atas
        }
    }   

    protected function cek_pca($pca, int $company_code)
    {
        $Trading = ['L', 'M', 'E'];
        $Surabaya = ['R124', 'P201', 'C201', 'P206', 'C206'];
        $Marunda = ['R120', 'P200', 'C200', 'P205', 'C205'];
        $Medan = ['P202', 'C202', 'P207', 'C207'];
        $Consumer = ['C203'];
        $Tarjun = ['R130'];
        $Sbe = ['E100', 'R521'];

        // untuk SOCI
        if($company_code === 5600){
            return 'SOCI';
        }

        if(in_array(substr($pca,0,1), $Trading)){
            return 'Trading';
        }else if(in_array($pca, $Surabaya)){
            return 'Surabaya';
        }else if(in_array($pca, $Marunda)){
            return 'Marunda';
        }else if(in_array($pca, $Medan)){
            return 'Medan';
        }else if(in_array($pca, $Consumer)){
            return 'Consumer';
        }else if(in_array($pca, $Tarjun)){
            return 'Tarjun';
        }else if(in_array($pca, $Sbe)){
            return 'Sbe';
        }
    }
}
