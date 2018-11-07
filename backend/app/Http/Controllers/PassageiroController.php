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
	
	public function entrarCorrida(Request $request){
		$user = auth()->user();
		$id = $user['id'];
		$id_corrida = $request['id_corrida'];
		return $this->passageiro->setCorrida($id,$id_corrida);
	}
	public function corridaAtual(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		return $this->passageiro->getCorridaAtual($id);
	}
	public function sairCorrida(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		return $this->passageiro->deleteCorridaAtual($id);
	}
	public function feed(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$tipo = $usuario['tipo'];
		$filtro = [intval($request['filtroGenero']),$request['filtroSaida'],$request['filtroHora']];
		if($filtro[0] == 2 and $filtro[1] == "" and $filtro[2] == ""){
			$filtro = [];
		}
		return $this->passageiro->getCorridas($id,$tipo,$filtro);
	}
	public function historico(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		return $this->passageiro->getHistorico($id);
	}

}
