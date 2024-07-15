<?php

namespace App\Http\Controllers;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Request;
use App\Models\Login;
class AuthController extends Controller
{
  /**
  * Create a new AuthController instance.
  *
  * @return void
  */
  public function __construct()
  {
    // $this->middleware('auth:api', ['except' => ['login','register','validateToken']]);
  }

  /**
  * Get a JWT via given credentials.
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function validateToken(Request $request){
    return $this->respondWithToken($request->token);
  }

  public function register(Request $request){
    return response()->json(["result"=>$request->email,"message"=>""]);
  }

  public function login()
  {
    $credentials = request(['email', 'password']);

    if (! $token = auth()->attempt($credentials)) {
      return response()->json(['error' => 'Unauthorized'], 401);
    }
    $user = auth()->user()->only('id','name','email');
    $user['token'] = $token;
    $user['expiration'] = \Carbon\Carbon::now()->format('Y/m/d H:i:s');

    $login = new Login;
    $login->type = 'login';
    $login->start = \Carbon\Carbon::now()->format('Y/m/d H:i:s');
    $login->date_expiration = \Carbon\Carbon::now()->addMinutes('40')->format('Y/m/d H:i:s');
    $login->date_refresh = \Carbon\Carbon::now()->addMinutes('20')->format('Y/m/d H:i:s');
    $login->refresh = Null;
    $login->save();


    return response()->json(compact('user'));
    // return $this->respondWithToken($token);
  }

  /**
  * Get the authenticated User.
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function me()
  {
    return response()->json(auth()->user());
  }

  /**
  * Log the user out (Invalidate the token).
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function logout()
  {
    auth()->logout();

    return response()->json(['message' => 'Successfully logged out']);
  }

  /**
  * Refresh a token.
  *
  * @return \Illuminate\Http\JsonResponse
  */
  public function refresh()
  {
    $token = auth()->refresh();
    $user = auth()->user()->only('id','name','email');
    $user['token'] = $token;
    $user['expiration'] = \Carbon\Carbon::now()->format('Y/m/d H:i:s');

    $login = new Login;
    $login->type = 'refresh';
    $login->start = null;
    $login->date_expiration = Null;
    $login->date_refresh = Null;
    $login->refresh = \Carbon\Carbon::now()->format('Y/m/d H:i:s');
    $login->save();

    return response()->json(compact('user'));
    // return $this->respondWithToken(auth()->refresh());
  }

  /**
  * Get the token array structure.
  *
  * @param  string $token
  *
  * @return \Illuminate\Http\JsonResponse
  */
  protected function respondWithToken($token)
  {
    return response()->json([
      'access_token' => $token,
      'token_type' => 'bearer',
      'expires_in' => auth()->factory()->getTTL() * 1440
    ]);
  }
//   'expires_in' => auth()->factory()->getTTL() * 60


}
