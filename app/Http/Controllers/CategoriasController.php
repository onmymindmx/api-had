<?php namespace App\Http\Controllers;

use App\Categoria;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

use JWTAuth;

class CategoriasController extends Controller {


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
		$categorias = Categoria::all();
		return $categorias;
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
			return response()->json(['status' => 'error', 'error' => 'No puede crear una nueva categoria.'], 401);
		}

		$categoria = new Categoria();
		$categoria->nombre = Input::get('nombre');
		$categoria->icono = Input::get('icono');
		$categoria->descripcion = Input::get('descripcion');
		if($categoria->save()){
			return array('status'=>'Nueva categoria creada con éxito!');
		}

		return array('status' => 'error', 'error' => $categoria->getErrors());
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		$categoria = Categoria::find($id);
		return $categoria;
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
			return response()->json(['status' => 'error', 'error' => 'No puede editar una categoria.'], 401);
		}

		$categoria = Categoria::find($id);
		$categoria->nombre = Input::get('nombre');
		$categoria->icono = Input::get('icono');
		$categoria->descripcion = Input::get('descripcion');
		if($categoria->save()){
			return array('status' => 'Categoria actualizada con éxito!');
		}

		return array('status' => 'error', 'error' => $categoria->getErrors());
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
			return response()->json(['status' => 'error', 'error' => 'No puede eliminar una categoria.'], 401);
		}

		if(Categoria::destroy($id)){
			return array('status' => 'Categoria eliminada con éxito!');
		}

		return array('status' => 'No se pudo eliminar la categoría');
	}

}
