<?php namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Watson\Validating\ValidatingTrait;

class Lugar extends Model {

    use ValidatingTrait;

    protected $table = 'lugares';

    protected $fillable = ['nombre', 'categoria', 'direccion'];

    protected $geofields = array('coordenadas');

    protected $rules = [
        'nombre' => 'required',
        'categoria' => 'required|exists:categorias,id',
        'subcategoria' => 'exists:subcategorias,id',
        'direccion' => 'required',
        'coordenadas' => 'required',
        'user' => 'required',
    ];

    protected $validationMessages = [
        'nombre.required' => "El lugar necesita un nombre.",
        'categoria.required' => "El lugar necesta una categoria.",
        'categoria.exists' => "La categoria no existe.",
        'subcategoria.exists' => "La subcategoria no existe.",
        'direccion.required' => "El lugar necesita una dirección.",
        'coordenadas.required' => "Debe de mencionar las coordenadas del lugar.",
        'user.required' => "El lugar debe de tener un propietario."
    ];

	public function categoria() {
        return $this->belongsTo('App\Categoria', 'categoria')->select('id', 'nombre')->get()->first();
    }

    public function subcategoria() {
        return $this->belongsTo('App\Subcategoria', 'subcategoria')->select('id', 'nombre')->get()->first();
    }

    public function user() {
        return $this->belongsTo('App\User', 'user')->select('id', 'first_name', 'last_name', 'username', 'email')->get()->first();
    }

    public function setCoordenadasAttribute($value) {
        $this->attributes['coordenadas'] = DB::raw("POINT($value)");
    }

    public function getCoordenadasAttribute($value) {
        $loc = substr($value, 6);
        $loc = preg_replace('/[ ,]+/', ',', $loc, 1);
        return substr($loc, 0, -1);
    }

    public function newQuery($excludeDeleted = true) {
        $raw = '';
        foreach($this->geofields as $column) {
            $raw .= ' astext('.$column.') as '.$column.' ';
        }

        return parent::newQuery($excludeDeleted)->addSelect('*',DB::raw($raw));
    }

    public function getLatLng(){
        if(!$coordenadas = $this->attributes['coordenadas']){
            return null;
        }
        $loc = substr($coordenadas, 6);
        $loc = preg_replace('/[ ,]+/', ',', $loc, 1);
        $loc = substr($loc, 0, -1);
        $loc = explode(',', $loc);
        $latLng = array("lat"=>(float)$loc[0], "lng" =>(float)$loc[1]);
        return $latLng;
    }
}
