<?php

use App\Http\Controllers\ExcelSampleController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/samples/excel/crates', [ExcelSampleController::class, 'downloadCratesSample'])
    ->name('samples.excel.crates');
