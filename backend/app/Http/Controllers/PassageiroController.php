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
		$infos = \DB::table('pessoa')
						->where('id',$id)
						->get();
		return $infos;
	}
	public function setCorrida(Request $request){
		$user = auth()->user();
		$id = $user['id'];
		$id_corrida = $request['id_corrida'];
		$corrida = \DB::table('corridas')->where('id',$id_corrida)->get();
		$corrida = json_decode($corrida, true);
		$vagas = $corrida[0]['vagas'];
		if(self::emCorrida()){
			return response()->json([
				'status' => 'error', 
				'message' => 'Você já está numa corrida'
			]);
		}else{
			if($vagas>0){
				\DB::table('corrida_ativa')->insert(array(
												'id_corrida' => $id_corrida,
												'id_passageiro' => $id
											));
				\DB::table('corridas')->where('id',$id_corrida)->update(['vagas' => $vagas-1]);
				return response()->json([
					'status' => 'success', 
					'message' => 'Você entrou na corrida com sucesso.'
				]);
			}else{
				return response()->json([
					'status' => 'error', 
					'message' => 'Não há vagas nessa corrida.'
				]);
			}
		}
	}
	public function getCorridaAtual(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$corrida = \DB::table('corrida_ativa')
									->join('corridas', 'corrida_ativa.id_corrida', '=', 'corridas.id')
									->join('motorista', 'corridas.id_motorista', '=', 'motorista.id')
									->join('pessoa', 'motorista.id_usuario', '=', 'pessoa.id')
									->select('corridas.id','corridas.saida','corridas.pontoEncontro','corridas.data_hora','corridas.vagas',
									'pessoa.nome','motorista.modelo','motorista.placa','motorista.corCarro','motorista.nota',\DB::raw('group_concat(corrida_ativa.id_passageiro) as passageiros'))
									->havingRaw('Find_In_Set(?, passageiros)',[$id])
									->groupBy('corrida_ativa.id_corrida')
									->orderBy('corridas.id','desc')
									->take(1)
									->get();
		return $corrida;
	}
	public function sairCorrida(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$corridaAtual = self::getCorridaAtual();
		$corridaAtual = json_decode($corridaAtual, true);
		$id_corrida = $corridaAtual[0]['id'];
		\DB::table('corrida_ativa')
					->where('id_passageiro',$id)
					->where('id_corrida',$id_corrida)
					->delete();
		return response()->json([
					'status' => 'success', 
					'message' => 'Você saiu da corrida com sucesso.'
				]);
	}
	public function emCorrida(){
		$corrida = self::getCorridaAtual();
		if($corrida->isEmpty()){
			return false;
		}else{
			return true;
		}
	}
	public function getCorridas(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$tipo = $usuario['tipo'];
		if($tipo==1){
			$corrida = self::getCorridaAtual();
			$feed = \DB::table('corridas')
									->leftjoin('corrida_ativa', 'corridas.id', '=', 'corrida_ativa.id_corrida')
									->join('motorista', 'corridas.id_motorista', '=', 'motorista.id')
									->join('pessoa', 'motorista.id_usuario', '=', 'pessoa.id')
									->select('corridas.id','corridas.saida','corridas.pontoEncontro','corridas.data_hora','corridas.vagas',
									'pessoa.nome','motorista.modelo','motorista.placa','motorista.corCarro','motorista.nota',\DB::raw('group_concat(corrida_ativa.id_passageiro) as passageiros'))
									->groupBy('corridas.id')
									->orderBy('corridas.id','desc')
									->where('status',0)
									->orderBy('id', 'desc')
									->take(10)
									->get();		
									
			
			if($corrida->isEmpty()){
				return $feed;
			}else{
				return json_encode(array_merge(json_decode($corrida, true),json_decode($feed, true)));
			}
		}
	}
	public function getHistorico(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		$corridasHistorico = \DB::table('historico')
									->join('corridas', 'historico.id_corrida', '=', 'corridas.id')
									->join('motorista', 'corridas.id_motorista', '=', 'motorista.id')
									->join('pessoa', 'motorista.id_usuario', '=', 'pessoa.id')
									->select('corridas.id','corridas.saida','corridas.pontoEncontro','corridas.data_hora','corridas.vagas',
									'pessoa.nome','motorista.modelo','motorista.placa','motorista.corCarro','motorista.nota',\DB::raw('group_concat(historico.id_passageiro) as passageiros'))
									->havingRaw('Find_In_Set(?, passageiros)',[$id])
									->groupBy('historico.id_corrida')
									->orderBy('corridas.id','desc')
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
