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
					'genero' => $request['genero'],
					'email' => $request['email'],
					'password' => HASH::make($request['password']), 
					'codigo_validacao' => $request['codigo_validacao'],
					'tipo' => $request['tipo']
					];
		$linha = \DB::table('pessoa')->where('codigo_validacao', $usuario['codigo_validacao'])->first();
		if(intval($usuario['tipo']) == 2){
			$usuario += ['modelo' => $request['modelo'],
						 'corCarro' => $request['corCarro'],
						 'placa' =>  $request['placa'],
						 'nota' => 0
						];
			\DB::table('motorista')->insert(
				array(
					'id_usuario' => $linha->id,
					'modelo' => $usuario['modelo'],
					'placa' => $usuario['placa'],
					'corCarro' => $usuario['corCarro'],
					'nota' => $usuario['nota']
				)		
			);
		}
		\DB::table('pessoa')->where('id',$linha->id)->update(
			array(
				'nome' => $usuario['nome'],
				'nascimento' => $usuario['nascimento'],
				'genero' => $usuario['genero'],
				'email' => $usuario['email'], 
				'password' => $usuario['password'],
				'codigo_validacao' => '',
				'tipo' => $usuario['tipo'])
		);
	}
}
