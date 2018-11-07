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
Route::get('/registerFinal',function(){
	return view('registerFinal');
});
Route::get('/',function(){
	return view('login');
});

Route::post('/preCadastro', 'Controller@preCadastro');
Route::post('/', 'AuthController@login');

Route::group(['middleware' => 'jwt.auth'], function () {
	Route::post('/cadastroFinalUsuario', 'Controller@cadastroFinal');
    Route::get('/indexUsuario', 'Controller@verPerfil');
	Route::post('/editarUsuario', 'Controller@editarPerfil');
	Route::post('/checarCadastroUsuario', 'Controller@checarCadastro');
	Route::post('/mudarTipoPerfilUsuario', 'Controller@mudarTipoPerfil');
	
	Route::post('/criarCorridaMotorista','MotoristaController@criarCorrida');
	Route::get('/corridaAtualMotorista','MotoristaController@corridaAtual');
	Route::get('/historicoMotorista','MotoristaController@historico');
	Route::get('/cancelarCorridaMotorista','MotoristaController@cancelarCorrida');
	Route::post('/editarCorridaMotorista','MotoristaController@editarCorrida');
	Route::get('/concluirCorridaMotorista','MotoristaController@concluirCorrida');
	
	Route::post('/feedPassageiro', 'PassageiroController@feed');
	Route::get('/historicoPassageiro', 'PassageiroController@historico');
	Route::post('/entrarCorridaPassageiro', 'PassageiroController@entrarCorrida');
	Route::get('/sairCorridaPassageiro', 'PassageiroController@sairCorrida');

});