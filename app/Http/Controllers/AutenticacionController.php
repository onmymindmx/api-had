<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Response;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class AutenticacionController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => ['restricted']]);
    }

	public function signup()
    {
        $email = Input::only('email');
        $username = Input::only('username');

        if(User::where('email', $email)->first()) {
            return Response::json(['message' => 'Este correo ya está registrado.'], 401);
        }

        if(User::where('username', $username)->first()) {
            return Response::json(['message' => 'Este nombre de usuario ya está utilizado.'],401);
        }

        $credentials = Input::only('email', 'password', 'username');
        $credentials = array('email' => $credentials['email'], 'password' => $credentials['password'], 'username' => $credentials['username']);
        try {
            $user = User::create($credentials);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()], 500);
        }
        $claims = ['user' => $user];
        $token = JWTAuth::fromUser($user, $claims);
        return Response::json(compact('token'), 201);
    }

    public function login()
    {
        $credentials = Input::only('email','password');
        try {
            // attempt to verify the credentials and create a token for the user
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Contraseñas incorrectas'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong whilst attempting to encode the token
            return Response::json(['error' => 'No pudimos accederte al sistema'], 500);
        }

        // all good so return the token
        $user = JWTAuth::toUser($token);
        $claims = ['user' => $user];
        $token = JWTAuth::attempt($credentials, $claims);
        return Response::json(compact('token'), 200);
    }

    public function restricted()
    {
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

}
