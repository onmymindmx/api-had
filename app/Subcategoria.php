<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Watson\Validating\ValidatingTrait;


class Subcategoria extends Model {

	use ValidatingTrait;

    protected $rules = [
        'nombre' =>  'required',
        'categoria' => 'required|exists:categorias,id'
    ];

    protected $validationMessages = [
        'nombre.required' => "La subcategoria necesita un nombre.",
        'categoria.required' => "Necesita seleccionar una categoria.",
        'categoria.exists' => "La categoria no existe."
    ];

    public function categoria(){
        return $this->belongsTo('App\Categoria', 'categoria')->select('id','nombre')->get()->first();
    }

}
