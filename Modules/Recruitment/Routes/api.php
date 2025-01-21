<?php

use Illuminate\Support\Facades\Route;
use Modules\Recruitment\Http\Controllers\AssistantController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->prefix('recruitment')->group( function () {
    Route::post('create-assistant', [AssistantController::class, 'store'])->name('recruitment.create-assistant');  
});
