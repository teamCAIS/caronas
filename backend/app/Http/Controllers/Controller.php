<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function cadastro(Request $request){
		\DB::table('pessoa')->insert(
			array('nome' => $request['nome'],'nascimento' => date('Y-m-d', strtotime(str_replace('-', '/', $request['nascimento']))),
			'cpf' => $request['cpf'],'email' => $request['email'], 'password' => HASH::make($request['password']),'tipo' => $request['tipo'])
		);
	}
}
