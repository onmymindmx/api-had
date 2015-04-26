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

class AutenticacionController extends Controller {

	public function signup()
    {
        $credentials = Input::only('email', 'password');
        $credentials = array('email' => $credentials['email'], 'password' => $credentials['password']);
        try {
            $user = User::create($credentials);
        } catch (Exception $e) {
            return Response::json(['error' => $e->getMessage()]);
        }

        return $user;
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
        $claims = ['email' => $user];
        $token = JWTAuth::attempt($credentials, $claims);
        return Response::json(compact('token','user'));
    }

    public function whoami(){
        try {

            if (! $user = JWTAuth::parseToken()->authenticate()) {
                return response()->json(['user_not_found'], 404);
            }

        } catch (TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());

        } catch (TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());

        } catch (JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());

        }

        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'));
    }

}
