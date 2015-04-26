<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;

class Categoria extends Model {

	use ValidatingTrait;

	protected $table = "categorias";

	protected $rules = [
		'nombre' => 'required'
	];

	protected $validationMessages = [
		'nombre.required' => "La categoria necesita un nombre."
	];

}
