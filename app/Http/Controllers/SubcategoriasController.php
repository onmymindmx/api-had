<?php namespace App\Http\Controllers;

use App\Categoria;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Subcategoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class SubcategoriasController extends Controller {


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
		$subcategorias = Subcategoria::all();
		foreach($subcategorias as $subcategoria ){
			$subcategoria->categoria = $subcategoria->categoria();
		}
		return $subcategorias;
	}


	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{

		$user = JWTAuth::parseToken()->authenticate();
		if(!$user->isAdmin) {
			return response()->json(['status' => 'error', 'error' => 'No puede crear una subcategoria.'], 401);
		}

		$subcategoria = new Subcategoria;
		$subcategoria->nombre = Input::get('nombre');
		$subcategoria->icono = Input::get('icono');
		$subcategoria->descripcion = Input::get('descripcion');
		$subcategoria->categoria = Input::get('categoria');

		if($subcategoria->save()){
			return array('status' => 'Subcategoria creada con éxito!');
		}

		return array('status' => 'error', 'error' => $subcategoria->getErrors());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$subcategoria = Subcategoria::find($id);

		$subcategoria->categoria = $subcategoria->categoria();
		return $subcategoria;
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
		if(!$user->isAdmin) {
			return response()->json(['status' => 'error', 'error' => 'No puede actualizar una subcategoria.'], 401);
		}

		$subcategoria = Subcategoria::find($id);
		$subcategoria->nombre = Input::get('nombre');
		$subcategoria->icono = Input::get('icono');
		$subcategoria->descripcion = Input::get('descripcion');
		$subcategoria->categoria = Input::get('categoria');

		if($subcategoria->save()){
			return array('status' => 'Subcategoria actualizada con éxito!');
		}

		return array('status' => 'error', 'error' => $subcategoria->getErrors());
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
		if(!$user->isAdmin) {
			return response()->json(['status' => 'error', 'error' => 'No puede eliminar una subcategoria.'], 401);
		}

		if(Subcategoria::destroy($id)){
			return array('status' => 'Subcategoria eliminada con éxito');
		}

		return array('status' => 'No se pudo eliminar la categoría');
	}

}
