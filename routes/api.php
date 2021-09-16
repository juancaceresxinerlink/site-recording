<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RecordingController;


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



$router->options(
    '/{any:.*}',
    [
        'middleware' => ['cors'],
        function () {
            return response(['status' => 'success']);
        },
    ]
);

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

//Route::get('queues', [RecordingController::class, 'getQueues2'])

//Route::middleware('auth:api')->get('/queues', [RecordingController::class, 'getQueues2']);


//$router->get('queues', 'RecordingController@getQueues2');

