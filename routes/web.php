<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataMonitoringController;
use App\Http\Controllers\RuntimeController;

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

Route::get('get-data/{date1}/{date2}', [DataMonitoringController::class, 'getdata'])->name('firebase.getdata');
Route::get('today-stat', [DataMonitoringController::class, 'todaystat'])->name('firebase.todaystat');

Route::get('this-week-data', [DataMonitoringController::class, 'thisweekdata'])->name('firebase.thisweekdata');
Route::get('weekly-data/{date}', [DataMonitoringController::class, 'weeklydata'])->name('firebase.weeklydata');
Route::get('this-month-data', [DataMonitoringController::class, 'thismonthdata'])->name('firebase.thismonthdata');

Route::get('this-week-runtime', [RuntimeController::class, 'thisweekruntime'])->name('firebase.thisweekruntime');
Route::get('weeklyruntime/{date}', [RuntimeController::class, 'weeklyruntime'])->name('firebase.weeklyruntime');
Route::get('this-month-runtime', [RuntimeController::class, 'thismonthruntime'])->name('firebase.thismonthruntime');
Route::get('monthlyruntime/{date}', [RuntimeController::class, 'monthlyruntime'])->name('firebase.monthlyruntime');
Route::get('this-year-runtime', [RuntimeController::class, 'thisyearruntime'])->name('firebase.thisyearruntime');
Route::get('annualruntime/{date}', [RuntimeController::class, 'annualruntime'])->name('firebase.annualruntime');
