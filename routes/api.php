<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\TaskController;

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

Route::post('register', [ ApiController::class, 'register' ]);
Route::post('login', [ ApiController::class, 'login' ]);

Route::group(['middleware' => ['jwt.verify']], function() {
    Route::get('/tasks', [ TaskController::class, 'select' ]);
    Route::post('/task', [ TaskController::class, 'create' ]);
    Route::put('/task/{taskId}', [ TaskController::class, 'update' ]);
    Route::delete('/task/{taskId}', [ TaskController::class, 'delete' ]);
});
