<?php



use App\Http\Controllers\GoogleSheetsController;
use Illuminate\Support\Facades\Route;

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

Route::get('googlesheet', [GoogleSheetsController::class, 'sheetOperation']);
Route::get('todaystat', [GoogleSheetsController::class, 'todayStat']);
Route::get('weeklydata', [GoogleSheetsController::class, 'weeklyData']);
Route::get('weeklyerror', [GoogleSheetsController::class, 'weeklyError']);
Route::get('errorlog', [GoogleSheetsController::class, 'errorLog']);
