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
	
	public function criarCorrida(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$horario = 'Y-m-d '.$request['horario'];
		$data = date($horario);
		$infos = ['saida' => $request['saida'], 'horario' => $data, 'pontoEncontro' => $request['pontoEncontro'], 'vagas' => intval($request['vagas'])];
		return $this->motorista->setCorrida($id,$infos);
	}
	public function corridaAtual(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		return $this->motorista->getCorridaAtual($id);
	}
	public function editarCorrida(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$horario = 'Y-m-d '.$request['horario'];
		$data = date($horario);
		$infos = ['saida' => $request['saida'], 'horario' => $data, 'pontoEncontro' => $request['pontoEncontro'], 'vagas' => intval($request['vagas'])];
		return $this->motorista->editCorrida($id,$infos);
	}
	public function concluirCorrida(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		return $this->motorista->concludeCorrida($id);
	}
	public function historico(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		return $this->motorista->getHistorico($id);
	}
	public function cancelarCorrida(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		return $this->motorista->deleteCorrida($id);
	}
}
