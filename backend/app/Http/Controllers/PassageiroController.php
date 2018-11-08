<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use App\Passageiro; 
class PassageiroController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	protected $passageiro;
	
	public function __construct(Passageiro $obj) {  
        $this->passageiro = $obj;
    }
	public function checkTipo(){
		$user = auth()->user();
		$tipo = $user['tipo'];
		if($tipo==2){
			return false;
		}else{
			return true;
		}
	}
	public function entrarCorrida(Request $request){
		$user = auth()->user();
		$id = $user['id'];
		$check = self::checkTipo();
		if($check){
			$id_corrida = $request['id_corrida'];
			return $this->passageiro->setCorrida($id,$id_corrida);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um motorista.'
			]);
		}
	}
	public function corridaAtual(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$check = self::checkTipo();
		if($check){
			return $this->passageiro->getCorridaAtual($id);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um motorista.'
			]);
		}
	}
	public function sairCorrida(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		return $this->passageiro->deleteCorridaAtual($id);
	}
	public function feed(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$check = self::checkTipo();
		if($check){
			$filtro = [intval($request['filtroGenero']),$request['filtroSaida'],$request['filtroHora']];
			if($filtro[0] == 2 and $filtro[1] == "" and $filtro[2] == ""){
				$filtro = [];
			}
			return $this->passageiro->getCorridas($id,$filtro);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um motorista.'
			]);
		}
	}
	public function historico(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$check = self::checkTipo();
		if($check){
			return $this->passageiro->getHistorico($id);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um motorista.'
			]);
		}
	}
	public function avaliar(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$check = self::checkTipo();
		if($check){
			$avaliacao = ['id_corrida'=>intval($request['id_corrida']),'nota'=> floatval($request['nota']),'status_nota'=>intval($request['status_nota'])];
			return $this->passageiro->setAvaliacao($id,$avaliacao);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você é um motorista.'
			]);
		}
	}
}
