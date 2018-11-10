<?php
namespace App;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\Authenticatable as AuthenticableTrait;

class Admin extends Model implements JWTSubject,Authenticatable
{
    use AuthenticableTrait;
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $table = 'admin';
	protected $fillable = [
		'email', 'password','token_access'
	];
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var array
	 */
	protected $hidden = [
		'password',
	];
	/**
	 * Get the identifier that will be stored in the subject claim of the JWT.
	 * @return mixed
	 */
	public function getJWTIdentifier()
	{
		return $this->getKey();
	}
	/**
	 * Return a key value array, containing any custom claims to be added to the JWT.
	 * @return array
	 */
	public function getJWTCustomClaims()
	{
		return [];
	}
}