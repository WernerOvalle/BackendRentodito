<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tiendas extends Model
{

    protected $table = "Tiendas";
    //Relacion de uno a muchos inversa
      public function Articulos(){
          return $this->hasMany('App\Articulos');
      }

}
