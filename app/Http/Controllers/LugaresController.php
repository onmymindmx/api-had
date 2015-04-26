<?php namespace App\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Lugar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class LugaresController extends Controller {

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
		$lugar = new Lugar;
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
		if(Lugar::destroy($id)) {
			return array('status' => 'Lugar eliminado con éxito.');
		}

		return array('status' => 'No se pudo eliminar el lugar.');
	}

}
