<?php

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

Route::get('/admin',function(){
	return view('admin-login');
})->name('admin.login')->middleware('adminsession');
Route::post('/admin', 'AdmController@login');

Route::group(['middleware' => 'auth.unique.user'], function () {
	Route::get('/adm_index','AdmController@getPreCadastros')->name('admin.index');
	Route::get('/adm_validarusuario/{id}', 'AdmController@aceitar')->name('admin.validarUsuario');
	Route::get('/adm_recusarusuario/{id}', 'AdmController@recusar')->name('admin.recusarUsuario');
	Route::get('/adm_logout/', 'AdmController@logout')->name('admin.logout');
});