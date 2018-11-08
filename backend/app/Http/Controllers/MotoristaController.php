<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use App\Motorista; 
class MotoristaController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	protected $motorista;
	
	public function __construct(Motorista $obj) {  
        $this->motorista = $obj;
    }
	public function checkTipo(){
		$user = auth()->user();
		$tipo = $user['tipo'];
		if($tipo==1){
			return false;
		}else{
			return true;
		}
	}
	public function criarCorrida(Request $request){
		$usuario = auth()->user();
		$check = self::checkTipo();
		if($check){
			$id = $usuario['id'];
			$horario = 'Y-m-d '.$request['horario'];
			$data = date($horario);
			$infos = ['saida' => $request['saida'], 'horario' => $data, 'pontoEncontro' => $request['pontoEncontro'], 'vagas' => intval($request['vagas'])];
			return $this->motorista->setCorrida($id,$infos);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um passageiro.'
			]);
		}
	}
	public function corridaAtual(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$check = self::checkTipo();
		if($check){
			return $this->motorista->getCorridaAtual($id);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um passageiro.'
			]);
		}
	}
	public function editarCorrida(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$check = self::checkTipo();
		if($check){
			$horario = 'Y-m-d '.$request['horario'];
			$data = date($horario);
			$infos = ['saida' => $request['saida'], 'horario' => $data, 'pontoEncontro' => $request['pontoEncontro'], 'vagas' => intval($request['vagas'])];
			return $this->motorista->editCorrida($id,$infos);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um passageiro.'
			]);
		}
	}
	public function concluirCorrida(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$check = self::checkTipo();
		if($check){
			return $this->motorista->concludeCorrida($id);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um passageiro.'
			]);
		}
	}
	public function historico(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$check = self::checkTipo();
		if($check){
			return $this->motorista->getHistorico($id);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um passageiro.'
			]);
		}
	}
	public function cancelarCorrida(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$check = self::checkTipo();
		if($check){
			return $this->motorista->deleteCorrida($id);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um passageiro.'
			]);
		}
	}
}
