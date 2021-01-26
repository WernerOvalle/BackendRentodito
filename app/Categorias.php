<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Categorias extends Model
{
    protected $table = "Categorias";
    //Relacion de uno a muchos inversa
      public function Articulos(){
          return $this->hasMany('App\Articulos');
      }


}
