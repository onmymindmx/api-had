<?php namespace App\Http\Controllers;

use App\Categoria;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Lugar;
use App\Subcategoria;
use Illuminate\Http\Request;

class DashboardController extends Controller {

	public function home() {
        $countCategorias = Categoria::all()->count();
        $countSubcategorias = Subcategoria::all()->count();
        $countLugares = Lugar::all()->count();

        $response = [
            'categorias' => $countCategorias,
            'subcategorias' => $countSubcategorias,
            'lugares' => $countLugares
        ];

        return $response;

    }

}
