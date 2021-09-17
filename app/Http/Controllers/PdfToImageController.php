<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Org_Heigl\Ghostscript\Ghostscript;
use Spatie\PdfToText\Pdf;

class PdfToImageController extends Controller
{
    public function index()
    {
        Ghostscript::setGsPath("C:\Program Files\gs\gs9.54.0\bin\gswin64c.exe");
        $pathToPdf = base_path('public/storage/pdf/Deal_Slip_-_10.09.2021 (1) (3).pdf');
        echo Pdf::getText($pathToPdf, 'c:/Program Files/Git/mingw64/bin/pdftotext');

    }
}
