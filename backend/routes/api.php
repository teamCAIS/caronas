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
    Route::get('/index', 'Controller@pegaInfo');
});