<?php

namespace App\Http\Middleware;

use Closure;

class CheckUserUniqueAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
		$usuario = \DB::table('admin')->select('token_access')->where('id',1)->get();
		$usuario = json_decode($usuario,true);
		if ($usuario[0]['token_access'] != $request->session()->get('access_token')) {
			
			return redirect()
						->route('admin.login')
						->with('message', 'A sessão deste usuário está ativa em outro local!');
		}
        return $next($request);
    }
}
