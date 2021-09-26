<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Str;
use Spatie\PdfToImage\Pdf;
use Org_Heigl\Ghostscript\Ghostscript;

class UploadSoalController extends Controller
{
    public function index()
    {
        return view('upload-soal.index');
    }

    public function upload(Request $request)
    {
        if($request->file('file')->getClientOriginalExtension() === 'pdf'){
            $path = $request->file('file')->store('public/pdf');
            $nama_file = Str::between($path,'public/pdf/', '.pdf');

            Ghostscript::setGsPath("C:\Program Files\gs\gs9.54.0\bin\gswin64c.exe");
            $pdf = new Pdf(base_path('public/storage/pdf/'.$nama_file.'.pdf'));

            $pdf->saveImage(base_path('public/storage/image/'.$nama_file.'.png'));
            $nama_file = $nama_file.'.png';
            $image = base_path('public/storage/image/'.$nama_file);
            Storage::delete($path);
        }else{
            $path = $request->file('file')->store('public/image');
            $nama_file = Str::after($path, 'public/image/');
            $image = base_path("public/storage/image/".$nama_file) ;
        }
        $total = (int) $request->total;

        // olah jadi text
        $text = (new TesseractOCR($image))
                ->lang('eng+equ')
                ->allowlist(range('A', 'Z'), range('a', 'z'), range(0, 9), '=.()/-_@', ' ')
                ->run();
        echo $text; die;
        $soal = Str::between($text, '.', '(A)');
        $a = Str::between($text, '(A)', 'm');
        $b = Str::between($text, '(B)', 'm');
        $c = Str::between($text, htmlentities('&#169'), 'm');
        $d = Str::between($text, '(D)', 'm');
        $e = Str::after($text, '(E)');
        echo "<p>".$soal."</p>";
        echo "<p> A. ".$a."</p>";
        echo "<p> B. ".$b."</p>";
        echo "<p> C. ".$c."</p>";
        echo "<p> D. ".$d."</p>";
        echo "<p> E. ".$e."</p>";
        die;
        // dd($text);
        var_dump($text);
        echo $text; die;
    }
}
