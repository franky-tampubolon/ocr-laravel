<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Str;
use Spatie\PdfToImage\Pdf;
use Org_Heigl\Ghostscript\Ghostscript;

class DealslipController extends Controller
{
    public function index(){
        return view('template.dealslip');
    }

    public function save(Request $request)
    {
        // upload file and save to storage
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
        $text = (new TesseractOCR($image))->run();



        // get quantity and make it to integer
        $qty = Str::between($text, 'QTY', 'KG');
        $qty = (int) trim(Str::replace('.', '', Str::after($qty, '/')));

        // get percentage of payment and make it to integer
        $payment = Str::after($text, 'PAYMENT');
        $payment = (int) trim(Str::before($payment, '%'));

        // get deal price and make it to integer
        $deal_price = Str::after($text, 'DEAL PRICE');
        $deal_price = (int) trim(Str::replace('.', '', Str::before($deal_price, 'IDR')));

        // get include VAT and make it to integer
        $incl_vat = Str::after($text, '(INCL VAT)');
        $incl_vat = (int) trim(Str::replace('.', '', Str::before($incl_vat, 'IDR')));

        // count total price of deal price and include VAT
        $hitung_total = (int) ($payment * $qty * $deal_price)/100;
        $total_incl_vat = (int) ($payment * $qty * $incl_vat)/100;

        // check text total and compare to input total
        if( $total === $hitung_total){
            $price = $deal_price;
            return response()->json([
                'price' => $price,
                'qty' => $qty,
                'payment' => $payment,
                'img' => $nama_file
            ]);
        }elseif($total === $total_incl_vat){
            $price = $incl_vat;
            return response()->json([
                'price' => $price,
                'qty' => $qty,
                'payment' => $payment,
                'img' => $nama_file
            ]);
        }else{
            $price = 0;
            return response()->json([
                'price' => $price,
                'qty' => $qty,
                'payment' => $payment,
                'img' => $nama_file
            ]);
        }
    }

    public function tampil(Request $request)
    {
        // dd($request->all());
        $validate = Validator::make($request->all(), [
            'harga' => 'required|numeric|min:1',
            'persen' => 'required|numeric|max:100',
            'qty' => 'required|numeric|min:1',
            'images' => 'required'
        ], [
            'required' => ':attribute harus diisi',
            'numeric' => ':attribute harus berupa angka',
            'min' => ':attribute tidak boleh nol',
            'max' => ':attribute maksimum 100'
        ]);
        if($validate->fails()){
            return redirect()->route('dealslip.index')->with('error', 'Harga, persen, kuantiti tidak boleh nol');
        }else{
            return view('template.image', [
                'price' => $request->harga,
                'qty' => $request->qty,
                'payment' => $request->persen,
                'img' => asset('storage/image/'.$request->images)
            ]);
        }

    }

    public function crop_image(Request $request)
    {
        if($request->hasFile('file')){
            $request->file('file')->move('storage/image', $request->file('file')->getClientOriginalName());
            return response()->json(['status'=>true]);
        }else{
            return response()->json(['status'=>false]);
        }

    }
}
