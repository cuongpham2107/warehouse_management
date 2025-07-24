

<?php

use App\Http\Controllers\ExcelSampleController;
use Illuminate\Support\Facades\Route;



Route::get('/samples/excel/crates', [ExcelSampleController::class, 'downloadCratesSample'])
    ->name('samples.excel.crates');


Route::get('shipping-request/{id}/preview-invoice', [\App\Http\Controllers\ShippingRequestPreviewController::class, 'show'])->name('shipping-request.preview-invoice');
