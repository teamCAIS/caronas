<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
use JWTAuth;
use Validator;
class AuthController extends Controller
{
	/**
	 * API Login, on success return JWT Auth token
	 * @param Request $request
	 * @return JsonResponse
	 */
	public function login(Request $request)
	{
		auth()->shouldUse('pessoas');
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
			// Attempt to verify the credentials and create a token for the user
			if (! $token = JWTAuth::attempt($credentials)) {
				return response()->json([
					'status' => 'error', 
					'message' => 'Usuário não encontrado'
				], 401);
			}
		} catch (JWTException $e) {
			// Something went wrong with JWT Auth.
			return response()->json([
				'status' => 'error', 
				'message' => 'Falha no login, tente novamente.'
			], 500);
		}
		// All good so return the token
		return response()->json([
			'status' => 'Sucesso', 
			'data'=> [
				'token' => $token
				// You can add more details here as per you requirment. 
			]
		]);
	}
	/**
	 * Logout
	 * Invalidate the token. User have to relogin to get a new token.
	 * @param Request $request 'header'
	 */
	public function logout(Request $request) 
	{
		// Get JWT Token from the request header key "Authorization"
		$token = $request->header('Authorization');
		// Invalidate the token
		try {
			JWTAuth::invalidate($token);
			return response()->json([
				'status' => 'success', 
				'message'=> "User successfully logged out."
			]);
		} catch (JWTException $e) {
			// something went wrong whilst attempting to encode the token
			return response()->json([
			  'status' => 'error', 
			  'message' => 'Failed to logout, please try again.'
			], 500);
		}
	}
}