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
        'descripcion' => 'required'
    ];

    protected $validationMessages = [
        'nombre.required' => "El lugar necesita un nombre.",
        'categoria.required' => "El lugar necesta una categoria.",
        'categoria.exists' => "La categoria no existe.",
        'subcategoria.exists' => "La subcategoria no existe.",
        'direccion.required' => "El lugar necesita una dirección.",
        'coordenadas.required' => "Debe de mencionar las coordenadas del lugar.",
        'user.required' => "El lugar debe de tener un propietario.",
        'descripcion.required' => "El lugar debe de llevar una descripción."
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

    public function getDistance($coords){
        $coordsUser = explode(',', $coords);
        $latUser = $coordsUser[0];
        $lngUser = $coordsUser[1];
        $coordsPlace = explode(',', $this->getCoordenadasAttribute($this->attributes['coordenadas']));
        $latPlace = $coordsPlace[0];
        $lngPlace = $coordsPlace[1];
        $distance = $this->calculateDistance($latUser, $lngUser, $latPlace, $lngPlace);
        return $distance;

    }

    /**
     * @param $latUser
     * @param $lngUser
     * @param $latPlace
     * @param $lngPlace
     * @param int $radioTierra
     * @return float
     * @link http://stackoverflow.com/a/10054282
     */
    function calculateDistance($latUser, $lngUser, $latPlace, $lngPlace, $radioTierra = 6371000){
        // Convertimos los grados a radiales
        $latUser = deg2rad($latUser);
        $lngUser = deg2rad($lngUser);
        $latPlace = deg2rad($latPlace);
        $lngPlace = deg2rad($lngPlace);

        $lngDelta = $lngPlace - $lngUser;
        $a = pow(cos($latPlace) * sin ($lngDelta), 2) +
            pow(cos($latUser) * sin ($latPlace) - sin($latUser) * cos($latPlace) * cos($lngDelta), 2);
        $b = sin($latUser) * sin($latPlace) + cos($latUser) * cos($latPlace) * cos($lngDelta);

        $angle = atan2(sqrt($a), $b);
        $distance = $angle * $radioTierra;
        return round($distance);
    }
}
