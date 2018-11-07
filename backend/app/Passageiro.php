<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Passageiro extends Model 
{
    public function setCorrida($id_passageiro,$id_ride){
		$id = $id_passageiro;
		$id_corrida = $id_ride;
		$corrida = \DB::table('corridas')->where('id',$id_corrida)->get();
		$corrida = json_decode($corrida, true);
		$vagas = $corrida[0]['vagas'];
		if(self::emCorrida($id)){
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
	public function emCorrida($id){
		$corrida = self::getCorridaAtual($id);
		if($corrida->isEmpty()){
			return false;
		}else{
			return true;
		}
	}
	public function getCorridaAtual($id_passageiro){
		$id = $id_passageiro;
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
	public function getCorridas($id_passageiro, $tipo_perfil, $filtragem){
		$id = $id_passageiro;
		$tipo = $tipo_perfil;
		$filtros = $filtragem;
		if($tipo==1){
			$corrida = self::getCorridaAtual($id);
			$feed = \DB::table('corridas')
							->leftjoin('corrida_ativa', 'corridas.id', '=', 'corrida_ativa.id_corrida')
							->join('motorista', 'corridas.id_motorista', '=', 'motorista.id')
							->join('pessoa', 'motorista.id_usuario', '=', 'pessoa.id')
							->select('corridas.id','corridas.saida','corridas.pontoEncontro','corridas.data_hora','corridas.vagas',
							'pessoa.nome','pessoa.genero','motorista.modelo','motorista.placa','motorista.corCarro','motorista.nota',\DB::raw('group_concat(corrida_ativa.id_passageiro) as passageiros'))
							->groupBy('corridas.id')
							->orderBy('corridas.id','desc')
							->where(['corridas.status' => 0,['corridas.vagas','>',0]])
							->orderBy('id', 'desc')
							->take(10);
			if(!empty($filtros)){
				$genero = $filtros[0];
				$saida = $filtros[1];
				$hora = $filtros[2];		
				switch($genero){
					case 0:
						$feed->where('pessoa.genero',0);
						break;
					case 1:
						$feed->where('pessoa.genero',1);
						break;
				}							
				if($saida != ''){
					$feed->where('corridas.saida',$saida);
				}
				if($hora != ''){
					$feed->whereRaw('hour(corridas.data_hora) >= ?',$hora);
				}
				$feed = $feed->get();
			}
			if($corrida->isEmpty()){
				if(empty($filtros))
					$feed = $feed->get();
				return $feed;
			}else{
				if(empty($filtros))
					$feed = $feed->get();
				return json_encode(array_merge(json_decode($corrida, true),json_decode($feed, true)));
			}
		}
	}
	public function getHistorico($id_passageiro){
		$id = $id_passageiro;
		$corridasHistorico = \DB::table('historico')
									->join('corridas', 'historico.id_corrida', '=', 'corridas.id')
									->join('motorista', 'corridas.id_motorista', '=', 'motorista.id')
									->join('pessoa', 'motorista.id_usuario', '=', 'pessoa.id')
									->select('corridas.id','corridas.saida','corridas.pontoEncontro','corridas.data_hora','corridas.vagas',
									'pessoa.nome','motorista.modelo','motorista.placa','motorista.corCarro','motorista.nota',\DB::raw('group_concat(historico.id_passageiro) as passageiros'))
									->havingRaw('Find_In_Set(?, passageiros)',[$id])
									->groupBy('historico.id_corrida')
									->orderBy('corridas.id','desc')
									->where('corridas.status',1)
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
	public function deleteCorridaAtual($id_passageiro){
		$id = $id_passageiro;
		$corridaAtual = self::getCorridaAtual($id);
		if(!($corridaAtual->isEmpty())){
			$corridaAtual = json_decode($corridaAtual, true);
			$id_corrida = $corridaAtual[0]['id'];
			$vagas = $corridaAtual[0]['vagas'];
			\DB::table('corrida_ativa')
						->where('id_passageiro',$id)
						->where('id_corrida',$id_corrida)
						->delete();
			\DB::table('corridas')
						->where('id',$id_corrida)
						->update(['vagas' => $vagas+1]);
			
			return response()->json([
						'status' => 'success', 
						'message' => 'Você saiu da corrida com sucesso.'
					]);
		}else{
			return response()->json([
						'status' => 'error', 
						'message' => 'Você não está em nenhuma corrida no momento'
					]);
		}
	}
}