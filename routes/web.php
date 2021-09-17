<?php

use App\Http\Controllers\DealslipController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\PdfToImageController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::post('ocr/hasil', [OcrController::class, 'hasil'])->name('ocr.hasil');
Route::resource('ocr', OcrController::class);
Route::get('deal-slip', [DealslipController::class, 'index'])->name('dealslip.index');
Route::post('deal-slip/crop', [DealslipController::class, 'crop_image'])->name('dealslip.crop');
Route::post('deal-slip', [DealslipController::class, 'save'])->name('dealslip.save');
Route::post('deal-slip/tampil', [DealslipController::class, 'tampil'])->name('dealslip.tampil');

Route::get('/pdf', [PdfToImageController::class, 'index']);
