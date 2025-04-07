<?php

use App\Livewire\Feedback;
use App\Livewire\QueueForm;
use App\Livewire\QueuePending;
use App\Livewire\Welcome;
use App\Livewire\Admin;
use App\Livewire\UserFeedback;
use Illuminate\Support\Facades\Route;
use App\Livewire\Reports;
use App\Http\Controllers\ReportExportController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', QueueForm::class);
Route::get('/admin', Admin::class);
Route::get('/feedback', Feedback::class);
Route::get('/user-feedback', UserFeedback::class);
Route::get('/queue-pending/{queueId}', QueuePending::class);
Route::get('/reports', Reports::class);
Route::get('/export-reports', [App\Http\Controllers\ExportReportsController::class, 'index'])->name('export-reports');
Route::get('/reports/export/csv', [App\Http\Controllers\ReportExportController::class, 'exportCsv'])->name('reports.export.csv');
Route::get('/reports/export/json', [App\Http\Controllers\ReportExportController::class, 'exportJson'])->name('reports.export.json');
Route::get('/reports/export/weekly/csv', [App\Http\Controllers\ReportExportController::class, 'exportWeeklyCsv'])->name('reports.export.weekly.csv');
Route::get('/export-reports', [App\Http\Controllers\ExportReportsController::class, 'index'])->name('export-reports');



