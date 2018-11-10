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
	public function checkAvaliacao($id_passageiro){
		$id =  $id_passageiro;
		$corridaNaoAvaliada = \DB::table('historico')
									->select('id_corrida')
									->where(['id_passageiro'=>$id,'status_nota'=>0])
									->get();
		if($corridaNaoAvaliada->isEmpty()){
			return false;
		}else{
			$corridaNaoAvaliada = json_decode($corridaNaoAvaliada,true);
			$corrida = \DB::table('corridas')
								->join('motorista', 'corridas.id_motorista', '=', 'motorista.id')
								->join('pessoa', 'motorista.id_usuario', '=', 'pessoa.id')
								->select('corridas.id','pessoa.nome','corridas.data_hora')
								->where('corridas.id',$corridaNaoAvaliada[0]['id_corrida'])
								->get();
			return $corrida;
		}
	}
	public function setAvaliacao($id_passageiro,$avaliacao){
		$id =  $id_passageiro;
		$id_corrida = $avaliacao['id_corrida'];
		$nota = $avaliacao['nota'];
		$status = $avaliacao['status_nota'];
		$corrida = \DB::table('historico')
								->where(['id_corrida'=>$id_corrida, 'id_passageiro'=>$id])
								->update(array('nota'=>$nota,'status_nota'=>$status));
		return response()->json([
			'status' => 'success', 
			'message' => 'Obrigado por avaliar o motorista.'
		]);
	}
	public function getCorridaAtual($id_passageiro){
		$id = $id_passageiro;
		$corrida = \DB::table('corrida_ativa')
									->join('corridas', 'corrida_ativa.id_corrida', '=', 'corridas.id')
									->join('motorista', 'corridas.id_motorista', '=', 'motorista.id')
									->join('pessoa', 'motorista.id_usuario', '=', 'pessoa.id')
									->select('corridas.id','corridas.saida','corridas.pontoEncontro','corridas.data_hora','corridas.vagas',
									'pessoa.nome','pessoa.url_foto','motorista.modelo','motorista.placa','motorista.corCarro','motorista.nota',\DB::raw('group_concat(corrida_ativa.id_passageiro) as passageiros'))
									->havingRaw('Find_In_Set(?, passageiros)',[$id])
									->groupBy('corrida_ativa.id_corrida')
									->orderBy('corridas.id','desc')
									->take(1)
									->get();
		if(!($corrida->isEmpty())){
			$corrida = json_decode($corrida,true);
			$data = explode(" ",$corrida[0]['data_hora']);
			$corrida[0]['data'] = $data[0];
			$corrida[0]['hora'] = $data[1];		
			$corrida[0]['atual'] = true;
			unset($corrida[0]['data_hora']);
			$passageiros = explode(",",$corrida[0]['passageiros']);
			$corrida[0]['passageiros'] = [];
			
			foreach($passageiros as $passageiro){
				$pessoa = \DB::table('pessoa')
							->select('nome','url_foto')
							->where('id',intval($passageiro))
							->get();
				$pessoa = json_decode($pessoa,true);
				array_push($corrida[0]['passageiros'],$pessoa[0]);
			}
			return $corrida;
		}else{
			return [];
		}
	}
	public function getCorridas($id_passageiro, $filtragem){
		$id = $id_passageiro;
		$filtros = $filtragem;
		$avaliacao = self::checkAvaliacao($id);
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
		if(!empty($corrida))
			$feed->where([['corridas.id','<>',intval($corrida[0]['id'])]]);
			
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
		}else{
			$feed = $feed->get();
		}
		$feed = json_decode($feed,true);
		$feedFinal = [];
		foreach($feed as $corridafeed){ 
			$data = explode(" ",$corridafeed['data_hora']);
			$corridafeed['data'] = $data[0];
			$corridafeed['hora'] = $data[1];		
			unset($corridafeed['data_hora']);
			$passageiros = explode(",",$corridafeed['passageiros']);
			$corridafeed['passageiros'] = [];
			
			foreach($passageiros as $passageiro){
				$pessoa = \DB::table('pessoa')
							->select('nome','url_foto')
							->where('id',intval($passageiro))
							->get();
				$pessoa = json_decode($pessoa,true);
				array_push($corridafeed['passageiros'],$pessoa[0]);
			}
			array_push($feedFinal,$corridafeed);
		}
		if(empty($feedFinal))
			$feedFinal = [];
		if(empty($corrida)){
			if($avaliacao==false){
				return $feedFinal;
			}else{
				$avaliacao = json_decode($avaliacao,true);
				$avaliacao[0]['avaliar'] = true;
				return json_encode(array_merge($avaliacao,$feedFinal));
			}
		}else{
			if($avaliacao==false){
				return json_encode(array_merge($corrida,$feedFinal));
			}else{
				$avaliacao = json_decode($avaliacao,true);
				$avaliacao[0]['avaliar'] = true;
				return json_encode(array_merge($avaliacao,$corrida,$feedFinal));
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
									'pessoa.nome','pessoa.url_foto','motorista.modelo','motorista.placa','motorista.corCarro','motorista.nota',\DB::raw('group_concat(historico.id_passageiro) as passageiros'))
									->havingRaw('Find_In_Set(?, passageiros)',[$id])
									->groupBy('historico.id_corrida')
									->orderBy('corridas.id','desc')
									->where('corridas.status',1)
									->get();
		$corridasHistorico = json_decode($corridasHistorico,true);
		$historicoFinal = [];
		foreach($corridasHistorico as $corrida){ 
			$data = explode(" ",$corrida['data_hora']);
			$corrida['data'] = $data[0];
			$corrida['hora'] = $data[1];		
			unset($corrida['data_hora']);
			$passageiros = explode(",",$corrida['passageiros']);
			$corrida['passageiros'] = [];
			
			foreach($passageiros as $passageiro){
				$pessoa = \DB::table('pessoa')
							->select('nome','url_foto')
							->where('id',intval($passageiro))
							->get();
				$pessoa = json_decode($pessoa,true);
				array_push($corrida['passageiros'],$pessoa[0]);
			}
			array_push($historicoFinal,$corrida);
		}
		
		if(empty($historicoFinal)){
			return response()->json([
				'status' => 'error', 
				'message' => 'Você não participou de nenhuma corrida ainda.'
			]);
		}else{
			return $historicoFinal;
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