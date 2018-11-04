<?php
namespace App;
use Illuminate\Database\Eloquent\Model;

class Motorista extends Model 
{
	public function getPerfil($id_motorista){
		$id = $id_motorista;
		$infos = \DB::table('pessoa')
						->leftjoin('motorista','pessoa.id','=','motorista.id_usuario')
						->select('pessoa.*','motorista.modelo','motorista.placa','motorista.corCarro','motorista.qtCorridas','motorista.nota')
						->where('pessoa.id',$id)
						->get();
		return $infos;
	}
}