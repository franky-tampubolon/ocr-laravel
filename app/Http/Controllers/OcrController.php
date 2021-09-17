<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Str;
use Spatie\PdfToImage\Pdf;
use Org_Heigl\Ghostscript\Ghostscript;

class OcrController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('template.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if($request->file('file')->getClientOriginalExtension() === 'pdf'){
            $path = $request->file('file')->store('public/pdf');
            // dd($path);
            $nama_file = Str::between($path,'public/pdf/', '.pdf');

            // dd($nama_file);
            Ghostscript::setGsPath("C:\Program Files\gs\gs9.54.0\bin\gswin64c.exe");
            $pdf = new Pdf(base_path('public/storage/pdf/'.$nama_file.'.pdf'));
            $pdf->saveImage(base_path('public/storage/image/'.$nama_file.'.png'));
            $nama_file = $nama_file.'.png';
            $image = base_path('public/storage/image/'.$nama_file);
            // dd($image);

        }else{
            $path = $request->file('file')->store('public/image');
            $nama_file = Str::after($path, 'public/image/');
            $image = base_path("public/storage/image/".$nama_file) ;
        }

        $total = (int) $request->total;

        $text = (new TesseractOCR($image))->run();
        // dd($text);
        // echo $text;


        $qty = Str::between($text, 'QTY', 'KG');
        // $qty = Str::between($text, 'PALM KERNEL /', 'KG');
        $qty = (int) trim(Str::replace('.', '', Str::after($qty, '/')));
        // $qty = (int) trim(Str::replace(',', '', Str::after($qty, '/')));
        // $qty = (int) 45000;

        // $payment = Str::after($text, 'PAYMENT');
        $payment = Str::after($text, 'PAYMENT');
        $payment = (int) trim(Str::before($payment, '%'));

        $deal_price = Str::after($text, 'DEAL PRICE');
        // $deal_price = Str::after($text, '(EXCL VAT)');
        $deal_price = (int) trim(Str::replace('.', '', Str::before($deal_price, 'IDR')));
        // $deal_price = (int) trim(Str::replace(',', '', Str::before($deal_price, 'IDR')));

        $hitung_total = (int) ($payment * $qty * $deal_price)/100;
        $incl_vat = Str::after($text, '(INCL VAT)');
        $incl_vat = (int) trim(Str::replace('.', '', Str::before($incl_vat, 'IDR')));

        $total_incl_vat = (int) ($payment * $qty * $incl_vat)/100;

        if( $total === $hitung_total){
            $price = $deal_price;
        }elseif($total === $total_incl_vat){
            $price = $incl_vat;
        }else{
            $price = 0;
        }
        // echo $text;
        // dd($qty, $payment, $deal_price);


        // dd($total, $hitung_total, $total_incl_vat);

        // if($total === $total_incl_vat){
        //     $price = $incl_vat;
        // }
        // echo $text;
        // dd($qty, $payment, $deal_price);
        // return response()->json([
        //     'price' => $price,
        //     'qty' => $qty,
        //     'payment' => $payment,
        //     'img' => $nama_file
        // ]);
        // return view('template.image', [
        //     'price' => 6759,
        //     'qty' => 150000,
        //     'payment' => 98,
        //     'img' => $nama_file
        // ]);
        return view('template.image', [
            'price' => $price,
            'qty' => $qty,
            'payment' => $payment,
            'img' => $nama_file
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function hasil($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
