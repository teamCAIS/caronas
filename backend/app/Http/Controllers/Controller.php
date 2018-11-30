<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use App\Usuario;
use Storage;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	protected $usuario;
	
	public function __construct(Usuario $obj) {  
        $this->usuario = $obj;
    }
	
	public function preCadastro(Request $request){
		$url = null;
		if($request->hasFile('documento') && $request->file('documento')->isValid()){
			$nome = uniqid(date('HisYmd'));
			$extensao = $request->documento->extension();
			$url = "{$request['email']}-{$nome}.{$extensao}";
			$upload = Storage::disk('google')->put($url, file_get_contents($request['documento']));
			$url = Storage::disk('google')->url($url);
			if (!$upload)
				return redirect()
							->back()
							->with('error', 'Erro ao fazer upload')
							->withInput();
		}
		if($url != null){
			$usuario = ['nome' => $request['nome'],
						'nascimento' => date('Y-m-d', strtotime(str_replace('-', '/', $request['nascimento']))),
						'genero' => $request['genero'],
						'email' => $request['email'],
						'password' => HASH::make($request['password']), 
						'url_documento' => $url
						];
			return $this->usuario->setPreCadastro($usuario);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'O documento comprobatorio é necessário.'
			]);
		}
		
	}
	public function cadastroFinal(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$perfil_usuario = ['url_foto' => "", 'tipo' => intval($request['tipo']),'codigo_validacao' => $request['codigo_validacao']];
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
		$nomeUsuario = $usuario['nome'];
		$tipo = $usuario['tipo'];
		$url = null;
		if($request->hasFile('image') && $request->file('image')->isValid()){
			$nome = uniqid(date('HisYmd'));
			$extensao = $request->image->extension();
			$url = "{$nome}.{$extensao}";
			$upload = Storage::disk('google')->put($url, file_get_contents($request['image']));
			$url = Storage::disk('google')->url($url);
			if (!$upload)
				return redirect()
							->back()
							->with('error', 'Erro ao fazer upload')
							->withInput();
		}
		if($url != null){
			$info_usuario = ['nome' => $request['nome'],
						'genero' => intval($request['genero']),
						'email' => $request['email'],
						'password' => HASH::make($request['password']),
						'url_foto' => $url
						];
		}else{
			$info_usuario = ['nome' => $request['nome'],
						'genero' => intval($request['genero']),
						'email' => $request['email'],
						'password' => HASH::make($request['password']), 
						'url_foto' => ""
						];
		}
		if($tipo==2){
			$info_usuario+=['modeloCarro' => $request['modeloCarro'],'corCarro'=>$request['corCarro'], 'placaCarro' => $request['placaCarro']];
		}
		return $this->usuario->setPerfil($id,$tipo,$info_usuario);
	}
	public function mudarTipoPerfil(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$tipo_perfil = $request['tipo'];
		return $this->usuario->setTipoPerfil($id,$tipo_perfil);
	}
	public function checarCadastro(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$codigo_validacao = $request['codigo_validacao'];
		$vazio = $this->usuario->checkCadastro($id,$codigo_validacao);
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
	public function checarTipo(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		return $this->usuario->checkTipo($id);
	}
	public function denunciar(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$denuncia = ['id_denunciado' => $request['id_denunciado'],'tipo' => $request['tipo'],'comentario'=> $request['comentario']];
		return $this->usuario->setDenuncia($id,$denuncia);
	}
	public function buscarPorNome(Request $request){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$nome = $request['nome'];
		return $this->usuario->setBuscaPorNome($id,$nome);
	}
}
