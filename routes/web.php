<?php

use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CarController;
use App\Http\Controllers\ScrapperController;
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

Route::post('/api/register',[UserController::class,'register']);
Route::post('/api/login',[UserController::class,'login']);

Route::post('/api/cars',[CarController::class,'index']);

Route::get('/scrap/yapo',[ScrapperController::class,'index']);

Route::post('/scrap/yapo',[ScrapperController::class,'scrapYapo']);
