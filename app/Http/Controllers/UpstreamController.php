<?php

namespace App\Http\Controllers;

use App\Exports\UpstreamVendorExport;
use App\Imports\UpstreamVendorImport;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Str;
// use Carbon\Carbon;

class UpstreamController extends Controller
{
    public function import(Request $request)
    {
        $data = $this->import_vendor($request->file('excel'));
        // dd($data);
        $jenis = 'Vendor';
        $name_file = $request->file('excel')->getClientOriginalName();
        $export = new UpstreamVendorExport($data, 'Rekap_'.$name_file);
        return Excel::download($export, 'Rekap_'.$name_file.'.xlsx');
    }

    protected function import_vendor($file)
    {
        $data = [];
        $datas = Excel::toCollection(new UpstreamVendorImport, $file);
        $new = Arr::except($datas[0], [0]);
        // dd($new);

        $collections = collect($new)->groupBy([24]);
        foreach($collections as $key => $row)
        {
            // dd($key);
            // cek PSM index 1
            $company_code = $row[0][0];
            $psm = $row[0][1];
            $sender_account = (string) $row[0][16];
            $psm = $this->cek_psm($psm, $company_code, $sender_account);
            // dd($psm);
            // if(Str::of($row[0][6])->contains('EXP')){
            //     $map = 'Hijau';
            // }else if(Str::of($row[0][21])->contains('HO')){
            //     // echo $ho;
            //     $map = 'Biru';
            // }else{
            //     $due_date = Carbon::parse(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row[0][8]));
            //     $map = $this->cek_map($due_date);
            //     // dd($map);
            // }

            // cek nominal
            $amount = (int) Str::after($row[count($row)-1][11], '-');
            // dd($amount);
            $amount = $this->cek_nominal($amount);
            if($amount === 'kurang 200 juta'){
                // cek apakah PO atau tidak melalui Purchasing Document
                if($row[0][6]){
                    $jenis_po = 'PO';
                }else{
                    $jenis_po = 'Non PO';
                }
                $data[$psm][$amount][$jenis_po][] = [
                    // 'warna_map' => $map,
                    'no_btd' => $key,
                    'amount' => (string) number_format( Str::after($row[count($row)-1][11], '-'),0,'.',','),
                    'psm' => $psm
                ];
            }else{
                $data[$psm][$amount][] = [
                    // 'warna_map' => $map,
                    'no_btd' => $key,
                    'amount' => (string) number_format(Str::after($row[count($row)-1][11], '-'),0,'.',','),
                    'psm' => $psm
                ];
            }
        }
        // dd($data);
        $new_data = [];
        foreach($data as $psm => $a)
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
            if(count($data) > 10){
                $x = array_chunk($data, 10, true);
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

    public function cek_psm($psm, $company_code, $sender_account)
    {
        $ppvc = [
                '0002315548',
                '106350019',
                '0002299747',
                '106356009',
                '0003764222',
                '106369003',
                '0045375889',
                '107394001',
                '0020890541',
                '1220005828614',
                '106688001',
                '0002149702',
                '106891001',
                '0002315521',
                '1030005076662',
                '106360006',
                '0034776897',
                '1220007134730',
                '106689008',
                '0005713331',
                '1030005078262',
                '106349002'
            ];
        $psm2 = [
            '0002325578',
            '107670009',
            '0002140721',
            '1030005103870',
            '106366004',
            '0002312735',
            '1030005103711',
            '106363005'
        ];
        $psm3 = [
            '0002324307', '1030004393001', '106362017'
        ];
        $psm5 = [
            '0002140608', '1030005099656', '106351007', '0002331737', '1030005099805', '106352003'
        ];
        $psm7 = [
            '0002313162', '1030004451544', '106362009', '0002312557', '1030005103847', '106366047'
        ];
        $smart_ho = [
            '0023799626', '106384002'
        ];

        if(in_array($sender_account, $ppvc)){
            return 'PPVC';
        }else if(in_array($sender_account, $psm2)){
            return 'PSM 2';
        }else if(in_array($sender_account, $psm3)){
            return 'PSM 3';
        }else if(in_array($sender_account, $psm5)){
            return 'PSM 5';
        }else if(in_array($sender_account, $psm7)){
            return 'PSM 7';
        }else if(in_array($sender_account, $smart_ho)){
            return 'SMART HO';
        }
    }
}
