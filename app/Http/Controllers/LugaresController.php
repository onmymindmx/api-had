<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Lugar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use JWTAuth;

class LugaresController extends Controller {


	public function __construct()
	{
		$this->middleware('jwt.auth', ['except' => ['index', 'show']]);
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		$lugares = Lugar::all();
		foreach($lugares as $lugar){
			$lugar->categoria = $lugar->categoria();
			$lugar->subcategoria = $lugar->subcategoria();
			if($lugar->coordenadas) {
				$lugar->latLng = $lugar->getLatLng();
			}
		}
		return $lugares;
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$user = JWTAuth::parseToken()->authenticate();

		$lugar = new Lugar;
		$lugar->nombre = Input::get('nombre');
		$lugar->categoria = Input::get('categoria');
		$lugar->subcategoria = Input::get('subcategoria');
		$lugar->telefono = Input::get('telefono');
		$lugar->direccion = Input::get('direccion');
		$lugar->facebook = $this->limpiarUrl(Input::get('facebook'), 'facebook');
		$lugar->twitter = $this->limpiarUrl(Input::get('twitter'), 'twitter');
		$lugar->instagram = $this->limpiarUrl(Input::get('instagram'), 'instagram');
		$lugar->website = Input::get('website');
		$lugar->coordenadas = Input::get('coordenadas');
		$lugar->user = $user->id;
		$lugar->descripcion = Input::get('descripcion');

		if($lugar->save()) {
			return array('status' => 'Lugar creado con éxito');
		}

		return array('status' => 'error', 'error' => $lugar->getErrors());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$lugar = Lugar::find($id);

		if($lugar->categoria != null) {
			$lugar->categoria = $lugar->categoria();
		}

		if($lugar->subcategoria) {
			$lugar->subcategoria = $lugar->subcategoria();
		}

		if($lugar->coordenadas) {
			$lugar->latLng = $lugar->getLatLng();
		}

		$lugar->user = $lugar->user();
		return $lugar;
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$user = JWTAuth::parseToken()->authenticate();
		$lugar = Lugar::find($id);

		$lugar->nombre = Input::get('nombre');
		$lugar->categoria = Input::get('categoria');
		$lugar->subcategoria = Input::get('subcategoria');
		$lugar->telefono = Input::get('telefono');
		$lugar->direccion = Input::get('direccion');
		$lugar->facebook = Input::get('facebook');
		$lugar->twitter = Input::get('twitter');
		$lugar->instagram = Input::get('instagram');
		$lugar->website = Input::get('website');
		$lugar->coordenadas = Input::get('coordenadas');
		$lugar->descripcion = Input::get('descripcion');

		if($lugar->user != $user->id && !$user->isAdmin){
			return response()->json(['status' => 'error', 'error' => 'No puede actualizar el lugar.'], 401);
		}

		if($lugar->save()) {
			return array('status' => 'Lugar actualizado con éxito');
		}

		return array('status' => 'error', 'error' => $lugar->getErrors());
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		$user = JWTAuth::parseToken()->authenticate();
		$lugar = Lugar::find($id);
		if($lugar->user != $user->id && !$user->isAdmin){
			return response()->json(['status' => 'error', 'error' => 'No puede eliminar el lugar.'], 401);
		}
		if(Lugar::destroy($id)) {
			return array('status' => 'Lugar eliminado con éxito.');
		}

		return array('status' => 'No se pudo eliminar el lugar.');
	}

	public function limpiarUrl($urlLugar, $pagina)
	{
		switch($pagina){
			case 'facebook':
				$urlBase = 'facebook.com';
				break;
			case 'twitter':
				$urlBase = 'twitter.com';
				break;
			case 'instagram';
				$urlBase = 'instagram.com';
				break;
		}

		$pos = stripos($urlLugar, $urlBase);
		if($pos !== false){
			$x = $pos + strlen($urlBase);
			return substr($urlLugar, $x);
		} else {
			return $urlLugar;
		}


	}

}
