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
		$cadastrado = self::checkCadastro($id,$perfil_usuario['codigo_validacao']);
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
	public function setDenuncia($id_usuario,$info_denuncia){
		$id = $id_usuario;
		$id_denunciado = $info_denuncia['id_denunciado'];
		$tipo_denuncia = $info_denuncia['tipo'];
		$comentario_denuncia = $info_denuncia['comentario'];
		if(count($id_denunciado)==1){
			\DB::table('denuncia')->insert(	
										array(
											'id_denunciante'=>$id,
											'id_denunciado'=>$id_denunciado[0],
											'tipo'=>$tipo_denuncia,
											'comentario'=>$comentario_denuncia,
											'data'=> date('Y-m-d H:i:s')
											));
		}else{
			foreach($id_denunciado as $id_denuncia){
				\DB::table('denuncia')->insert(	
										array(
											'id_denunciante'=>$id,
											'id_denunciado'=>$id_denuncia,
											'tipo'=>$tipo_denuncia,
											'comentario'=>$comentario_denuncia,
											'data'=> date('Y-m-d H:i:s')
											));
			}
		}
		return response()->json([
			'status' => 'success', 
			'message' => 'A denúncia será analisada o mais rápido possível, verifique seu e-mail.'
		]);
	}
	public function setBuscaPorNome($id_usuario,$nome){
		$id = $id_usuario;
		$nome_usuario = $nome;
		$query = \DB::table('pessoa')->select('id','nome','url_foto')->where([['nome','LIKE',"{$nome_usuario}%"],['id','<>',$id]])->get();
		if($query->isEmpty()){
			return response()->json([
				'status' => 'error', 
				'message' => 'Usuário não encontrado'
			]);
		}else{
			return $query;
		}
	}
	public function setPerfil($id_usuario, $tipo_usuario, $infos){
		$id = $id_usuario;
		$usuario = $infos;
		$tipo = $tipo_usuario;
		if($usuario['url_foto']!=""){
			\DB::table('pessoa')->where('id',$id)->update(
				array(
					'nome' => $usuario['nome'],
					'genero' => $usuario['genero'],
					'email' => $usuario['email'], 
					'password' => $usuario['password'],
					'url_foto' => $usuario['url_foto']
				)
			);
		}else{
			\DB::table('pessoa')->where('id',$id)->update(
				array(
					'nome' => $usuario['nome'],
					'email' => $usuario['email'], 
					'password' => $usuario['password'],
					'genero' => $usuario['genero'],
				)
			);	
		}
		if($tipo_usuario==2){
			\DB::table('motorista')->where('id_usuario',$id)->update(
				array(
					'modelo' => $usuario['modeloCarro'],
					'corCarro' => $usuario['corCarro'],
					'placa' => $usuario['placaCarro'],
				)
			);
		}
		return response()->json([
			'status' => 'success',
			'message' => 'Você editou seu perfil com sucesso'
		]);
	}
	public function setTipoPerfil($id_usuario,$tipo_perfil){
		$id = $id_usuario;
		$tipo = $tipo_perfil;
		\DB::table('pessoa')->where('id',$id)->update(
			array(
				'tipo' => $tipo
				)
			);
		if($tipo == 2){
			$query = \DB::table('motorista')->where('id_usuario',$id)->get();
			if($query->isEmpty()){
				return response()->json([
					'status' => 'success', 
					'message' => 'Você mudou de perfil com sucesso.',
					'primeiro' => true
				]);
			}else{
				return response()->json([
					'status' => 'success', 
					'message' => 'Você mudou de perfil com sucesso.'
				]);
			}
		}else{
			return response()->json([
					'status' => 'success', 
					'message' => 'Você mudou de perfil com sucesso.'
				]);
		}
	}
	public function checkCadastro($id,$codigo){
		$linha = \DB::table('pessoa')->where(['id'=>$id,'codigo_validacao'=>$codigo])->get();
		if($linha->isEmpty()){
			return true;
		}else{
			return false;
		}
	}
	public function checkTipo($id){
		$linha = \DB::table('pessoa')->select('tipo')->where('id',$id)->get();
		return $linha;
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