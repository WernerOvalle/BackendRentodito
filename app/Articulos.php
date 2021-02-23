<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Articulos extends Model
{
    protected $table = "Articulos";
  //Relacion de uno a muchos inversa
    public function user(){
        return $this->belongsTo('App\User','user_id');

    }



    public function Tiendas(){
        return $this->belongsTo('App\Tiendas','tienda_id');
    }


    public function Categorias(){
        return $this->belongsTo('App\Categorias','categoria_id');
    }
}
