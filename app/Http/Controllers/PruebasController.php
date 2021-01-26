<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Articulos;
use App\Categorias;
use App\Tiendas;
class PruebasController extends Controller
{
    public function index(){
        $titulo='Animales';
        $animales = ['PERRO','GATO'];
        return view('pruebas.index', array(
            'titulo' => $titulo,
            'animales' => $animales
        ));
    }


    public function testOrm(){
        $Catgorias = Categorias::all();
        foreach($Catgorias as $category){
            echo "<h1> {$category->name}</h1>";

        //$Articulo= Articulos::all();
        //var_dump($Articulo);
        foreach($category->Articulos as $art){
            echo "<h2>".$art->title."<h2>";
            echo "<span style='color: gray;'>{$art->user->name} - {$art->Tiendas->name} -{$art->Categorias->name}</span>";
          }
        die();
    }

}

}
