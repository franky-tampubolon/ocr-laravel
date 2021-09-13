<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Str;
use Spatie\PdfToText\Pdf;

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
        $path = $request->file('image')->store('public/image');
        $total = (int) $request->total;

        $nama_file = Str::after($path, 'public/image/');
        $image = base_path("public/storage/image/".$nama_file) ;

        $text = (new TesseractOCR($image))->run();
        // echo $text;

        $deal_price = Str::after($text, 'DEAL PRICE');
        $deal_price = (int) trim(Str::replace('.', '', Str::before($deal_price, 'IDR')));

        if($total === $deal_price){
            $price = $deal_price;
        }else{
            $incl_vat = Str::after($text, '(INCL VAT)');
            $price = (int) trim(Str::replace('.', '', Str::before($incl_vat, 'IDR')));
        }

        $qty = Str::between($text, 'COMMODITY/QTY', 'KG');
        $qty = (int) trim(Str::replace('.', '', Str::after($qty, '/')));

        $payment = Str::after($text, 'PAYMENT');
        $payment = (float) trim(Str::before($payment, '%'));
        

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
    public function show($id)
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
