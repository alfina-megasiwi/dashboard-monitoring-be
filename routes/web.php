<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DataMonitoringController;
use App\Http\Controllers\RuntimeController;
use App\Http\Controllers\ErrorController;

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
Route::get('this-year-data', [DataMonitoringController::class, 'thisyeardata'])->name('firebase.thisyeardata');

Route::get('this-week-runtime', [RuntimeController::class, 'thisweekruntime'])->name('firebase.thisweekruntime');
Route::get('weeklyruntime/{date}', [RuntimeController::class, 'weeklyruntime'])->name('firebase.weeklyruntime');
Route::get('this-month-runtime', [RuntimeController::class, 'thismonthruntime'])->name('firebase.thismonthruntime');
Route::get('monthlyruntime/{date}', [RuntimeController::class, 'monthlyruntime'])->name('firebase.monthlyruntime');
Route::get('this-year-runtime', [RuntimeController::class, 'thisyearruntime'])->name('firebase.thisyearruntime');
Route::get('annualruntime/{date}', [RuntimeController::class, 'annualruntime'])->name('firebase.annualruntime');

Route::get('get-errorlog', [ErrorController::class, 'getErrorLog'])->name('firebase.getErrorLog');
Route::get('get-week', [ErrorController::class, 'getweek'])->name('firebase.getweek');
Route::get('get-month', [ErrorController::class, 'getmonth'])->name('firebase.getmonth');
Route::get('get-year', [ErrorController::class, 'getyear'])->name('firebase.getyear');

Route::get('this-week-error', [ErrorController::class, 'thisweekerror'])->name('firebase.thisweekerror');
Route::get('this-month-error', [ErrorController::class, 'thismontherror'])->name('firebase.thismontherror');
Route::get('monthlyerror/{date}', [ErrorController::class, 'monthlyerror'])->name('firebase.monthlyerror');
Route::get('this-year-error', [ErrorController::class, 'thisyearerror'])->name('firebase.thisyearerror');
Route::get('annualerror/{date}', [ErrorController::class, 'annualerror'])->name('firebase.annualerror');
