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
		$usuario = ['nome' => $request['nome'],
					'nascimento' => date('Y-m-d', strtotime(str_replace('-', '/', $request['nascimento']))),
					'email' => $request['email'],
					'password' => HASH::make($request['password']), 
					'codigo_validacao' => $request['codigo_validacao'],
					'tipo' => $request['tipo']
					];
		$linha = \DB::table('pessoa')->where('codigo_validacao', $usuario['codigo_validacao'])->first();
		\DB::table('pessoa')->where('id',$linha->id)->update(
			array(
				'nome' => $usuario['nome'],
				'nascimento' => date('Y-m-d', strtotime(str_replace('-', '/', $request['nascimento']))),
				'email' => $request['email'], 
				'password' => HASH::make($request['password']),
				'codigo_validacao' => '',
				'tipo' => $request['tipo'])
		);
	}
	public function pegaInfo(){
		return auth() -> user();
	}
}
