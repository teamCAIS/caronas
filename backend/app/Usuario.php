<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Usuario extends Model 
{
	public function setPreCadastro($infos_usuario){
		$usuario = $infos_usuario;
		\DB::table('precadastro')->insert(
			array(
				'nome' => $usuario['nome'],
				'nascimento' => $usuario['nascimento'],
				'genero' => $usuario['genero'],
				'email' => $usuario['email'], 
				'password' => $usuario['password'],
				'url_documento' => $usuario['url_documento']
				)
		);
		return response()->json([
					'status' => 'success', 
					'message' => 'Sua solicitação de cadastrado foi enviada com sucesso'
				]);
	}
	
	public function setCadastro($id_usuario,$perfil){
		$id = $id_usuario;
		$perfil_usuario = $perfil;
		$cadastrado = self::checkCadastro($perfil_usuario['codigo_validacao']);
		if(!$cadastrado){
			if($perfil_usuario['tipo'] == 2){
				\DB::table('pessoa')->where('id',$id)->update(
					array(
						'codigo_validacao' => '',
						'url_foto' => $perfil_usuario['url_foto'],
						'tipo' => 2,
						'status' => 1
					)
				);
				\DB::table('motorista')->insert(
					array(
						'id_usuario' => $id,
						'modelo' => $perfil_usuario['modelo'],
						'placa' => $perfil_usuario['placa'],
						'corCarro' => $perfil_usuario['corCarro'],
						'nota' => $perfil_usuario['nota']
					)		
				);
			}else{
				\DB::table('pessoa')->where('id',$id)->update(
					array(
						'codigo_validacao' => '',
						'url_foto' => $perfil_usuario['url_foto'],
						'tipo' => 1,
						'status' => 1
					)
				);
			}
			return response()->json([
					'status' => 'success', 
					'message' => 'Cadastro finalizado'
				]);
		}else{
			return response()->json([
					'status' => 'error', 
					'message' => 'Código Inválido.'
				]);
		}
	}
	public function setPerfil($id_usuario, $infos){
		$id = $id_usuario;
		$usuario = $infos;
		if($usuario['url_foto']!=""){
			\DB::table('pessoa')->where('id',$id)->update(
				array(
					'nome' => $usuario['nome'],
					'nascimento' => date('Y-m-d', strtotime(str_replace('-', '/', $usuario['nascimento']))),
					'genero' -> $usuario['genero'],
					'email' => $usuario['email'], 
					'password' => HASH::make($usuario['password']),
					'tipo' => $usuario['tipo'],
					'codigo_validacao' => '',
					'url_foto' => $usuario['url_foto'],
					'status' => 1
				)
			);
		}else{
			\DB::table('pessoa')->where('id',$id)->update(
				array(
					'nome' => $usuario['nome'],
					'nascimento' => date('Y-m-d', strtotime(str_replace('-', '/', $usuario['nascimento']))),
					'genero' -> $usuario['genero'],
					'email' => $usuario['email'], 
					'password' => HASH::make($usuario['password']),
					'tipo' => $usuario['tipo'],
					'codigo_validacao' => '',
					'status' => 1
				)
			);	
		}
	}
	public function setTipoPerfil($id_usuario,$tipo_perfil){
		$id = $id_usuario;
		$tipo = $tipo_perfil;
		\DB::table('pessoa')->where('id',$id)->update(
			array(
				'tipo' => $tipo
				)
			);
		return response()->json([
				'status' => 'success', 
				'message' => 'Você mudou de perfil com sucesso.'
			]);
	}
	public function checkCadastro($codigo){
		$linha = \DB::table('pessoa')->where('codigo_validacao', $codigo)->get();
		if($linha->isEmpty()){
			return true;
		}else{
			return false;
		}
	}
	public function getPerfil($id_usuario, $tipo_usuario){
		$id = $id_usuario;
		$tipo = $tipo_usuario;
		if($tipo == 1){
			$infos = \DB::table('pessoa')
							->where('id',$id)
							->get();
		}else{
			$infos = \DB::table('pessoa')
							->leftjoin('motorista','pessoa.id','=','motorista.id_usuario')
							->where('pessoa.id',$id)
							->get();
		}
		return $infos;
	}
	public function setEsqueceuSenha(){
		
	}
}