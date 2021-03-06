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
            $export = new VendorExport($data, 'Rekap_'.$name_file);
            return Excel::download($export, 'Rekap_'.$name_file.'.xlsx');
        }
        $new_data = [];
        foreach($data as $values)
        {
            foreach($values['data'] as $value)
            {
                $new_data[] = $value;
            }
        }
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
        $datas = Excel::toCollection(new RekapImport, $file);
        $new = Arr::except($datas[0], [0]);
        // dd($new);
        $collections = collect($new)->groupBy([23]);
        foreach($collections as $key => $row)
        {
            // cek PCA index 1
            $company_code = $row[0][0];
            $pca = $row[0][1];
            $pca = $this->cek_pca($pca, $company_code);
            if(Str::of($row[0][6])->contains('EXP')){
                $map = 'Hijau';
            }else if(Str::of($row[0][21])->contains('HO')){
                // echo $ho;
                $map = 'Biru';
            }else{
                $due_date = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0][8]));
                $map = $this->cek_map($due_date);
                // dd($map);
            }

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
                $data[$pca][$map][$amount][$jenis_po][] = [
                    'warna_map' => $map,
                    'no_btd' => $key,
                    'amount' => (string) number_format(Str::after($row[count($row)-1][10], '-'),0,',','.'),
                    'pca' => $pca
                ];
            }else{
                $data[$pca][$map][$amount][] = [
                    'warna_map' => $map,
                    'no_btd' => $key,
                    'amount' => (string) number_format(Str::after($row[count($row)-1][10], '-'),0,',','.'),
                    'pca' => $pca
                ];
            }
        }
        // dd($data);
        $new_data = [];
        foreach($data as $pca => $a)
        {

            foreach($a as $warna => $b)
            {
                // dd($nominal);
                foreach($b as $nominal => $c)
                {
                    if($nominal === 'kurang 200 juta'){
                        foreach($c as $po => $d)
                        {
                            $new_data[] = $d;
                        }
                    }else{
                        $new_data[] = $c;
                    }
                }

            }
        }
        // dd($new_data);
        $final_array = [];
        foreach($new_data as $data)
        {
            if(count($data) > 8){
                $x = array_chunk($data, 8, true);
                foreach($x as $y){
                    $final_array[] = $y;
                }

            }else{
                $final_array[] = $data;
            }
        }
        // dd($final_array);
        return $final_array;
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
        if($now->dayOfWeek === Carbon::FRIDAY && $due_date->dayOfWeek === Carbon::MONDAY){
            return 'Kuning';
        }else{
            if($due_date->lessThanOrEqualTo($now->addDays(1))){
                return 'Kuning' ; //bayar besok
            }else {
                return 'Hijau'; //bayar lusa ke atas
            }
        }

    }

    protected function cek_pca($pca, $company_code)
    {
        // untuk SOCI
        if((int) $company_code === 5600){
            return 'SOCI';
        }
        // untuk OSM
        if((int) $company_code === 5500){
            return 'OSM';
        }

        // cek company code
        if(in_array((int) $company_code, [4600, 5200, 5300, 5400]) && substr($pca,0,1) === 'H'){
            return 'Trading';
        }

        // untuk Trading
        if(in_array((int) $company_code, [3300, 4600, 5200, 5300, 5400]) && in_array(substr($pca,0,1), ['L', 'M', 'E'])){
            return 'Trading';
        }
        // surabaya
        if((int) $company_code === 3300 && in_array($pca, ['R124', 'P201', 'C201', 'P206', 'C206'])){
            return 'Surabaya';
        }
        // Marunda
        if((int) $company_code === 3300 && in_array($pca, ['R120', 'R320', 'P200', 'C200', 'P205', 'C205'])){
            return 'Marunda';
        }
        // Medan
        if((int) $company_code === 3300 && in_array($pca, ['P202', 'C202', 'P207', 'C207'])){
            return 'Medan';
        }
        // Consumer
        if((int) $company_code === 3300 && in_array($pca, ['C203'])){
            return 'Consumer';
        }
        // Tarjun
        if((int) $company_code === 3300 && in_array($pca, ['R130', 'R230', 'R530', 'R232'])){
            return 'Tarjun';
        }
        // Belawan
        if((int) $company_code === 3300 && in_array($pca, ['R110', 'R210', 'R310'])){
            return 'Belawan';
        }
        // IMT
        if((int) $company_code === 5200 && in_array(substr($pca,0,1), ['R'])){
            return 'Imt';
        }
        // HO
        if((int) $company_code === 3300 && in_array(substr($pca,0,1), ['H'])){
            return 'HO';
        }
        // SIP
        if((int) $company_code === 4600 && in_array(substr($pca,0,1), ['R'])){
            return 'Sip';
        }
        // SBE
        if($company_code === 'AA00'){
            return 'Sbe';
        }
        // KMI
        if($company_code === 'AB00'){
            return 'Kmi';
        }
        // BAP
        if((int) $company_code === 5300 && in_array(substr($pca,0,1), ['R'])){
            return 'Bap';
        }
        // TAPIAN
        if((int) $company_code === 5400 && in_array(substr($pca,0,1), ['R'])){
            return 'Tapian';
        }
    }
}
