<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\RecordingController;


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
    return view('/auth/login');
});

Auth::routes();
  
Route::get('/home', [HomeController::class, 'index'])->name('home');
  
Route::group(['middleware' => ['auth']], function() {

    Route::resource('roles', RoleController::class);
    Route::resource('users', UserController::class);
    Route::resource('products', ProductController::class);
    Route::resource('Home', HomeController::class);
    //nueva forma de trabajar las rutas en laravel 8 
    Route::post('games', [HomeController::class, 'update']);

    //Obtiene Colas
    Route::get('api/queues', [RecordingController::class, 'getQueues2']);

    Route::get('api/agents', [RecordingController::class, 'GetAllAgent']);

    Route::get('api/agents', [RecordingController::class, 'GetAllAgent']);

    Route::get('/logout', '\App\Http\Controllers\Auth\LoginController@logout');

    //$router->get('recordings', 'RecordingController@index');


    Route::get('api/recordings', [RecordingController::class, 'index']);

    
    Route::get('api/recordings/audio/{id}', [RecordingController::class, 'getAudio']);

    //s3 join files

    Route::post('api/s3toURI', [RecordingController::class, 'getSftpToFile']);

    //Route::post('games', 'HomeController@update');
});
