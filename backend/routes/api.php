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

Route::get('/register',function(){
	return view('register');
});
Route::get('/',function(){
	return view('login');
});

Route::post('/cadastro', 'Controller@cadastro');
Route::post('/', 'AuthController@login');

Route::group(['middleware' => 'jwt.auth'], function () {
    Route::get('/indexPassageiro', 'PassageiroController@getInfo');
	Route::get('/feedPassageiro', 'PassageiroController@getCorridas');
	Route::get('/historicoPassageiro', 'PassageiroController@getHistorico');
	Route::get('/setCorridaPassageiro', 'PassageiroController@setCorrida');
	Route::get('/sairCorridaPassageiro', 'PassageiroController@sairCorrida');
	
});