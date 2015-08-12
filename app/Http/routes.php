<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix'=>'v1'], function()
{
	Route::resource('categorias', 'CategoriasController');
	Route::resource('subcategorias', 'SubcategoriasController');
	Route::resource('lugares', 'LugaresController');
	Route::get('dashboard', 'DashboardController@home');
	Route::get('mi-perfil', 'AutenticacionController@perfil');
	Route::get('mis-lugares', 'AutenticacionController@lugares');
});

Route::post('/login', 'AutenticacionController@login');
Route::post('/signup', 'AutenticacionController@signup');
Route::post('/recover-password', 'AutenticacionController@recoverPassword');
Route::post('/change-password', 'AutenticacionController@changePassword');