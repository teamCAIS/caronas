<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use App\Usuario;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	protected $usuario;
	
	public function __construct(Usuario $obj) {  
        $this->usuario = $obj;
    }
	
	public function preCadastro(Request $request){
		$usuario = ['nome' => $request['nome'],
					'nascimento' => date('Y-m-d', strtotime(str_replace('-', '/', $request['nascimento']))),
					'genero' => $request['genero'],
					'email' => $request['email'],
					'password' => HASH::make($request['password']), 
					'url_documento' => $request['url_documento']
					];
		return $this->usuario->setPreCadastro($usuario);
	}
	public function cadastroFinal(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$perfil_usuario = ['url_foto' => $request['url_foto'], 'tipo' => intval($request['tipo']),'codigo_validacao' => $request['codigo_validacao']];
		if($perfil_usuario['tipo'] == 2){
			$perfil_usuario += ['modelo' => $request['modelo'],
							 'corCarro' => $request['corCarro'],
							 'placa' =>  $request['placa'],
							 'nota' => 0
							];
		}
		return $this->usuario->setCadastro($id,$perfil_usuario);
	}
	public function verPerfil(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$tipo = $usuario['tipo'];
		return $this->usuario->getPerfil($id,$tipo);
	}
	public function editarPerfil(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$info_usuario = ['nome' => $request['nome'],
						'nascimento' => date('Y-m-d', strtotime(str_replace('-', '/', $request['nascimento']))),
						'genero' => $request['genero'],
						'email' => $request['email'],
						'password' => HASH::make($request['password']), 
						'tipo' => $request['tipo'],
						'url_foto' => $request['url_foto']
						];
		return $this->usuario->setPerfil($id,$info_usuario);
	}
	public function mudarTipoPerfil(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$tipo_perfil = $request['tipo'];
		return $this->usuario->setTipoPerfil($id,$tipo_perfil);
	}
	public function checarCadastro(Request $request){
		$codigo_validacao = $request['codigo_validacao'];
		$vazio = $this->usuario->checkCadastro($codigo_validacao);
		if(!$vazio){
			return response()->json([
				'status' => 'success', 
				'message' => 'O código existe'
			]);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'O código não existe'
			]);
		}
	}
}
