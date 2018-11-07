<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Motorista extends Model 
{
	public function setCorrida($id_usuario,$info_corrida){
		$id = $id_usuario;
		if(!self::emCorrida($id)){
			$id_motorista = self::getIdMotorista($id);
			$saida = $info_corrida['saida'];
			$horario = $info_corrida['horario'];
			$pontoEncontro = $info_corrida['pontoEncontro'];
			$vagas = $info_corrida['vagas'];
			$corrida = \DB::table('corridas')->insert(
										array(
											'id_motorista' => $id_motorista,
											'saida' => $saida,
											'pontoEncontro' => $pontoEncontro,
											'vagas' => $vagas,
											'data_hora' => $horario,
											'status' => 0
										)
									);
			return response()->json([
				'status' => 'success', 
				'message' => 'Você criou uma corrida com sucesso.'
			]);
		}else{
			return response()->json([
				'status' => 'error', 
				'message' => 'Você já está numa corrida.'
			]);
		}
	}
	public function editCorrida($id_usuario,$info_corrida){
		$id = $id_usuario;
		$id_motorista = self::getIdMotorista($id);
		$saida = $info_corrida['saida'];
		$horario = $info_corrida['horario'];
		$pontoEncontro = $info_corrida['pontoEncontro'];
		$vagas = $info_corrida['vagas'];
		$corrida = \DB::table('corridas')->where([['id_motorista','=',$id_motorista],['status','=',0]])->update(
									array(
										'saida' => $saida,
										'pontoEncontro' => $pontoEncontro,
										'vagas' => $vagas,
										'data_hora' => $horario
									)
								);
		return response()->json([
			'status' => 'success', 
			'message' => 'Você editou a corrida com sucesso.'
		]);
	}
	public function concludeCorrida($id_usuario){
		$id = $id_usuario;
		$id_motorista = self::getIdMotorista($id);
		$corridaAtual = self::getCorridaAtual($id);
		$corridaAtual = json_decode($corridaAtual,true);
		$passageiros = \DB::table('corrida_ativa')->where('id_corrida',$corridaAtual[0]['id'])->get();
		$passageiros = json_decode($passageiros,true);
		foreach($passageiros as $passageiro){
			\DB::table('historico')->insert(array('id_corrida'=>$corridaAtual[0]['id'],'id_passageiro'=>$passageiro['id_passageiro']));
			\DB::table('corrida_ativa')->where(['id_corrida'=>$corridaAtual[0]['id'],'id_passageiro'=>$passageiro['id_passageiro']])->delete();
		}
		
		$qtCorridas = \DB::table('motorista')->select('qtCorridas')->where('id',$id_motorista)->get();
		$qtCorridas = json_decode($qtCorridas,true);
		\DB::table('motorista')->where('id',$id_motorista)->update(array('qtCorridas' => intval($qtCorridas[0]['qtCorridas'])+1));
		
		$corrida = \DB::table('corridas')->where([['id_motorista','=',$id_motorista],['status','=',0]])->update(
									array(
										'status' => 1
									)
								);
		return response()->json([
			'status' => 'success', 
			'message' => 'Você concluiu corrida com sucesso.'
		]);
	}
	public function emCorrida($id_usuario){
		$id = $id_usuario;
		$corrida = self::getCorridaAtual($id);
		if($corrida->isEmpty()){
			return false;
		}else{
			return true;
		}
	}
	public function getIdMotorista($id_usuario){
		$id = $id_usuario;
		$query = \DB::table('pessoa')
						->leftjoin('motorista', 'pessoa.id','=','motorista.id_usuario')
						->select('motorista.id')
						->where('pessoa.id',$id)
						->get();
		$resultado = json_decode($query, true);
		return $resultado[0]['id'];
	}
	public function getCorridaAtual($id_usuario){
		$id = $id_usuario;
		$id_motorista = self::getIdMotorista($id);
		$corrida = \DB::table('corridas')
									->leftjoin('corrida_ativa', 'corridas.id', '=', 'corrida_ativa.id_corrida')
									->join('motorista', 'corridas.id_motorista', '=', 'motorista.id')
									->join('pessoa', 'motorista.id_usuario', '=', 'pessoa.id')
									->select('corridas.id','corridas.saida','corridas.pontoEncontro','corridas.data_hora','corridas.vagas',
									'pessoa.nome','motorista.modelo','motorista.placa','motorista.corCarro','motorista.nota',\DB::raw('group_concat(corrida_ativa.id_passageiro) as passageiros'))
									->groupBy('corrida_ativa.id_corrida')
									->orderBy('corridas.id','desc')
									->where(['corridas.id_motorista' => $id_motorista,['corridas.status','=',0]])
									->take(1)
									->get();
		return $corrida;
	}
	public function getHistorico($id_usuario){
		$id = $id_usuario;
		$id_motorista = self::getIdMotorista($id);
		$corridasHistorico = \DB::table('historico')
									->join('corridas', 'historico.id_corrida', '=', 'corridas.id')
									->join('motorista', 'corridas.id_motorista', '=', 'motorista.id')
									->join('pessoa', 'motorista.id_usuario', '=', 'pessoa.id')
									->select('corridas.id','corridas.saida','corridas.pontoEncontro','corridas.data_hora','corridas.vagas',
									'pessoa.nome','motorista.modelo','motorista.placa','motorista.corCarro','motorista.nota',\DB::raw('group_concat(historico.id_passageiro) as passageiros'))
									->groupBy('historico.id_corrida')
									->orderBy('corridas.id','desc')
									->where(['corridas.status' => 1,['motorista.id','=',$id_motorista]])
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
	public function deleteCorrida($id_usuario){
		$id = $id_usuario;
		$id_motorista = self::getIdMotorista($id);
		$corridaAtual = self::getCorridaAtual($id);
		if(!($corridaAtual->isEmpty())){
			$corridaAtual = json_decode($corridaAtual, true);
			$id_corrida = $corridaAtual[0]['id'];
			\DB::table('corridas')
						->where('id',$id_corrida)
						->delete();
			
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