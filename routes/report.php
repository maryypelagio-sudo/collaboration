<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportController;

Route::get('/report', [ReportController::class, 'index'])->name('report');

Route::get('/report/download', [ReportController::class, 'download'])->name('report.download');
