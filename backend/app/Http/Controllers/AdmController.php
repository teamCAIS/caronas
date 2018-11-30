<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use JWTAuth;
use Validator;
use View;
use Mail;

class AdmController extends Controller
{
	
	public function getPreCadastros(){
		$precadastros = \DB::table('precadastro')->select('*')->get();
		return view('adm_index',compact('precadastros'));
	}
	public function recusar($id){
		$pre = \DB::table('precadastro')->where('id',$id)->get();
		if($pre->isEmpty()){
			return false;
		}else{
			\DB::table('precadastro')->where('id',$id)->delete();
		}
		return redirect()->back();
	}
	public function aceitar($id){
		$pre = \DB::table('precadastro')->where('id',$id)->get();
		if($pre->isEmpty()){
			return false;
		}else{
			$pre = json_decode($pre,true);
			$pessoa = \DB::table('pessoa')->where(['email'=>$pre[0]['email'],'password'=>$pre[0]['password']])->get();
			if($pessoa->isEmpty()){
				$numero_de_bytes = 4;
				$restultado_bytes = random_bytes($numero_de_bytes);
				$resultado_final = bin2hex($restultado_bytes);
				$resultado_final = strtoupper($resultado_final);
				$novoUsuario = \DB::table('pessoa')->insert(array(
														'nome'=>$pre[0]['nome'],
														'nascimento'=>$pre[0]['nascimento'],
														'genero'=>$pre[0]['genero'],
														'email'=>$pre[0]['email'],
														'password'=>$pre[0]['password'],
														'codigo_validacao'=>$resultado_final,
														'status'=>0
														));
				self::email_codigo($pre[0]['nome'],$pre[0]['email'],$resultado_final);
				$pre = \DB::table('precadastro')->where('id',$id)->update(array('status'=>1));
			}
			return redirect()->back();
		}
	}
	public function email_codigo($nome,$email,$codigo){
		$data = ['body'=>"Olá! Seja bem-vindo ao Carpool SMD, um projeto da equipe CAIS em colaboração com a professora Mara Bonates. \n \n 
			Para acessar a aplicação, basta logar e inserir o código abaixo. \n \n Código de acesso: {$codigo} \n \n Que a força das caronas esteja com você"];
		$to = [
			'nome' => $nome,
			'email' => $email,
		];

		 Mail::raw($data['body'], function ($message) use($to){
				$message->to($to['email'],$to['nome']);
				$message->from('caisequipe@gmail.com', 'Equipe CAIS');
				$message->subject('Código de acesso - Carpool SMD');
		});
	}
	public function login(Request $request)
	{
		auth()->shouldUse('api');
		\Config::set('jwt.user', 'App\Admin'); 
        \Config::set('auth.providers.pessoas.model', \App\Admin::class);
		$email = $request->input('email');
		$password = $request->input('password');
		$credentials = ['email'=>$email, 'password'=>$password];
		$rules = [
			'email' => 'required|email',
			'password' => 'required',
		];
		$validator = Validator::make($credentials, $rules);
		if($validator->fails()) {
			return response()->json([
				'status' => 'error', 
				'message' => $validator->messages()
			]);
		}
		try {
			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json([
					'status' => 'error', 
					'message' => 'Usuário não encontrado'
				], 401);
			}
		} catch (JWTException $e) {
			return response()->json([
				'status' => 'error', 
				'message' => 'Falha no login, tente novamente.'
			], 500);
		}
		// All good so return the token
		\DB::table('admin')->where('email',$email)->update(array('token_access'=>$token));
		session()->put('access_token', $token);
		return redirect()->route('admin.index');
	}

	public function logout() 
	{
		session()->flush();
		session()->forget('access_token');
		return redirect()->route('admin.index');
	}
}