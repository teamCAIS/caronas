<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Motorista extends Model
{
    protected $table = 'motorista';
	protected $fillable = [
		'id_usuario', 'modelo','placa', 'corCarro','nota'
	];
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
	];
	/**
	 * Get the identifier that will be stored in the subject claim of the JWT.
	 * @return mixed
	 */
}
