<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\User;
use App\Lugar;
use App\Categoria;
use App\Subcategoria;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\View;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Illuminate\Support\Facades\DB;

use Hash;

class AutenticacionController extends Controller
{

    public function __construct()
    {
        $this->middleware('jwt.auth', ['only' => ['perfil', 'lugares']]);
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

        $credentials = Input::only('email', 'password', 'username', 'first_name', 'last_name');

        $credentials = array('email' => $credentials['email'], 'password' => $credentials['password'],
                            'username' => $credentials['username'], 'first_name' => $credentials['first_name'],
                            'last_name' => $credentials['last_name']);

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

    public function recoverPassword()
    {
        if(Input::has('email')){
            $email = Input::get('email');

            $user = User::where('email', $email);
            if(!$user->exists()){
                return response()->json(['success'=>false, 'error'=>'Usuario no encontrado'], 404);
            }

            $user = $user->first();
            $reset_code = str_random(60);
            $reset_token = str_random(10);
            $user->reset_code = $reset_code;
            $user->reset_token = $reset_token;
            if($user->save()){
                return view('emails.password', array('token'=>$reset_code));
            }

            return response()->json(['success'=>false, 'error' => 'No se pudo iniciar el proceso de restauración de contraseña'], 500);

        }

        if(Input::has('code')){
            $code = Input::get('code');
            $user = User::where(DB::raw('BINARY reset_code'), $code);
            if(!$user->exists()){
                return response()->json(['success'=>false, 'error'=>'invalid code'], 500);
            }
            $user = $user->first();

            return response()->json(['success'=>true, 'message'=>'valid code', 'token'=>$user->reset_token], 200);
        }

        return response()->json(['error'=>'Wrong request'], 500);
    }

    public function changePassword()
    {
        if(Input::has('reset_token')){
            $reset_token = Input::get('reset_token');
            $user = User::where(DB::raw('BINARY reset_token'), $reset_token);
            if(!$user->exists()){
                return response()->json(['success'=>false, 'error'=>'invalid token'], 500);
            }

            if(!Input::has('password')) {
                return response()->json(['success' => false, 'error' => 'Debe de escribir un password'], 500);
            }

            $user = $user->first();
            $user->password = Input::get('password');
            if($user->save()){
                return response()->json(['success'=>true, 'message'=>'Contraseña cambiada'], 200);
            }

            return response()->json(['success'=>false, 'error'=>'No se pudo cambiar la contraseña'], 500);
        }
    }

    public function perfil()
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

    public function lugares(){
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
        $places = Lugar::where('user', $user->id)->select('id', 'nombre', 'categoria', 'subcategoria')->get();
        foreach($places as $place){
            // Como nada mas obtenemos un elemento, lo guardamos en un array
            $categoria = Categoria::where('id', $place->categoria)->select('nombre')->get()->toArray();
            // Después, usamos reset rebobina el puntero interno al primer elemento y devolver el valor del primer elemento del array.
            $place->categoria = reset($categoria);

            $subcategoria = Subcategoria::where('id', $place->subcategoria)->select('nombre')->get()->toArray();
            $place->subcategoria = reset($subcategoria);
        }

        return response()->json($places);
    }

}
