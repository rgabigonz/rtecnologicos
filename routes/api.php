<?php

use Illuminate\Http\Request;

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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::apiResources([
    'user' => 'API\UserController'
]);

Route::get('profile', 'API\UserController@profile');
Route::put('profile', 'API\UserController@updateProfile');

Route::apiResources([
    'task' => 'API\TaskController'
]);

Route::put('state/{id}', 'API\TaskController@updateState');
Route::get('taskstat', 'API\TaskController@taskStatistics');

//Detalles de tareas
Route::get('taskdetail/{id}', 'API\TaskController@taskDetail');
Route::post('taskdetail', 'API\TaskController@storeDetail');