<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
class PassageiroController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	public function getInfo(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$infos = \DB::table('pessoa')->where('id',$id)->get();
		return $infos;
	}
	public function getCorridas(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$tipo = $usuario['tipo'];
		if($tipo==1){
			$corrida = \DB::table('corrida_ativa')->where('id_passageiro',$id)->get();
			if($corrida->isEmpty()){
				$corrida = \DB::table('corridas')->where('status',0)->orderBy('id', 'desc')->take(10)->get();
			}
			return $corrida;
		}
	}
	public function getHistorico(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$corridasHistorico = \DB::table('corridas')
									->join('historico', 'corridas.id', '=', 'historico.id_corrida')
									->select('corridas.*')
									->where('historico.id_passageiro', $id)
									->orderBy('id','desc')
									->get();
		if($corridasHistorico->isEmpty()){
			return response()->json([
				'status' => 'error', 
				'message' => 'Você não participou de nenhuma corrida ainda.'
			]);
		}else{
			return $corridasHistorico;
		}
	}
	public function setPerfil(Request $request){
		$user = auth()->user();
		$id = $user['id'];
		$usuario = ['nome' => $request['nome'],
					'nascimento' => date('Y-m-d', strtotime(str_replace('-', '/', $request['nascimento']))),
					'email' => $request['email'],
					'password' => HASH::make($request['password']), 
					'tipo' => $request['tipo']
					];
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
		\DB::table('pessoa')->where('id',$id)->update(
			array(
				'nome' => $usuario['nome'],
				'nascimento' => date('Y-m-d', strtotime(str_replace('-', '/', $request['nascimento']))),
				'email' => $request['email'], 
				'password' => HASH::make($request['password']),
				'codigo_validacao' => '',
				'tipo' => $request['tipo'])
		);
	}
}
