<?php

use App\Http\Controllers\DealslipController;
use App\Http\Controllers\ExcelController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OcrController;
use App\Http\Controllers\PdfToImageController;
use App\Http\Controllers\UploadSoalController;

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
    return view('excel.index');
});

Route::post('ocr/hasil', [OcrController::class, 'hasil'])->name('ocr.hasil');
Route::resource('ocr', OcrController::class);
Route::get('deal-slip', [DealslipController::class, 'index'])->name('dealslip.index');
Route::post('deal-slip/crop', [DealslipController::class, 'crop_image'])->name('dealslip.crop');
Route::post('deal-slip', [DealslipController::class, 'save'])->name('dealslip.save');
Route::post('deal-slip/tampil', [DealslipController::class, 'tampil'])->name('dealslip.tampil');

Route::get('excel', [ExcelController::class, 'index'])->name('excel.index');
Route::post('excel', [ExcelController::class, 'import'])->name('excel.import');

Route::get('/pdf', [PdfToImageController::class, 'index']);


Route::get('/upload-soal', [UploadSoalController::class, 'index']);
Route::post('/upload-soal', [UploadSoalController::class, 'upload'])->name('upload.store');

