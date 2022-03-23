<?php

namespace App\Http\Controllers;

use App\Exports\UpstreamVendorExport;
use App\Imports\UpstreamVendorImport;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;

class UpstreamController extends Controller
{
    public function import(Request $request)
    {
        $data = $this->import_vendor($request->file('excel'));

        $jenis = 'Vendor';
        $name_file = $request->file('excel')->getClientOriginalName();
        $export = new UpstreamVendorExport($data, 'Rekap_'.$name_file);
        return Excel::download($export, 'Rekap_'.$name_file.'.xlsx');
    }

    protected function import_vendor($file)
    {
        $data = [];
        $datas = Excel::toCollection(new UpstreamVendorImport, $file);
        // $new = Arr::except($datas[0], [0]);
        dd($datas);
    }
    public function cek_psm($sender_account)
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

    }
}
