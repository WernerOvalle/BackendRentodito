<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Middleware\ApiAuthMiddleware;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
//Rutas pruebas

Route::get('/', function () {
    return view('welcome');
});/*
Route::get('/prueba/{nombre?}', function ($nombre = null) {
    $texto = "<h1> texto </h1>";
    $texto .= 'nombre: ' . $nombre;
    return view('pruebas', array('texto' => $texto));
});

Route::get('/animales', 'PruebasController@index');


Route::get('/test-orm', 'PruebasController@testOrm');*/

/*pruebas api*/


/*
Route::get('/Articulos/pruebas', 'ArticulosController@pruebas');

Route::get('/Tiendas/pruebas', 'TiendasController@pruebas');

Route::get('/Usuario/pruebas', 'UserController@pruebas');

Route::get('/Categorias/pruebas', 'CategoriasController@pruebas');
Route::get('/Servicios/pruebas', 'SeviciosController@pruebas');
Route::get('/Personas/pruebas', 'PersonasController@pruebas');
*/

/*api usuarios*/
Route::post('api/register', 'UserController@register');
Route::post('api/login', 'UserController@login');
Route::put('api/user/update', 'UserController@update');
Route::post('api/user/upload1', 'UserController@upload1');//->middleware(ApiAuthMiddleware::class);
Route::post('api/user/upload2', 'UserController@upload2');//->middleware(ApiAuthMiddleware::class);
Route::post('api/user/upload3', 'UserController@upload3');//->middleware(ApiAuthMiddleware::class);
Route::get('api/user/avatar/{filename}', 'UserController@getImage');
Route::get('api/user/detail/{id}', 'UserController@detail');



/*Categorias*/
Route::resource('api/category', 'CategoriasController');

/*Articulos*/
Route::resource('api/articulos', 'ArticulosController');
Route::post('api/articulos/upload', 'ArticulosController@upload');
Route::get('api/articulos/image/{filename}', 'ArticulosController@getImage');
Route::get('api/articulos/categoria/{id}', 'ArticulosController@getArticulosByCateogoria');
Route::get('api/articulos/user/{id}', 'ArticulosController@getArticulosByUser');
