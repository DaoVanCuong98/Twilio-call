<?php

use App\Http\Controllers\IVRController;
use Illuminate\Support\Facades\Route;


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


Route::post('/',[IVRController::class,'showWelcome']);
Route::post('test',[IVRController::class,'showMenuResponse'])->name('test');
Route::post('ngay',[IVRController::class,'choosdate'])->name('ngay');
Route::post('menu-hour',[IVRController::class,'chooshour'])->name('menu-hour');
Route::post('hourAM',[IVRController::class,'chooshourAM'])->name('hourAM');
Route::post('hourPM',[IVRController::class,'chooshourPM'])->name('hourPM');
Route::post('confirm',[IVRController::class,'confirmcustomer'])->name('confirm');
