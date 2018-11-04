<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Hash;
use App\Motorista; 
class MotoristaController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
	
	protected $motorista;
	
	public function __construct(Motorista $obj) {  
        $this->motorista = $obj;
    }
	
	public function perfil(){
		$usuario = auth()->user();
		$id = $usuario['id'];
		return $this->passageiro->getPerfil($id);
	}
	
}
