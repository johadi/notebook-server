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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::get('/', function (){
   return response()->json("Welcome to notebook API");
});

// Authentication routes
Route::get('/user', 'AuthController@show');
Route::post('/login', 'AuthController@login');
Route::post('/signup', 'AuthController@register');
Route::get('/logout', 'AuthController@logout');

// Note routes
Route::get('/notes', 'NoteController@index');
Route::get('/note/{id}', 'NoteController@show');
Route::post('/note/create', 'NoteController@store');
Route::patch('/note/{id}', 'NoteController@update');
Route::delete('/note/{id}', 'NoteController@destroy');