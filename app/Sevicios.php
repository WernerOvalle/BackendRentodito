<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sevicios extends Model
{
      protected $table = "Servicios";
  //Relacion de uno a muchos inversa
    public function user(){
        return $this->belongsTo('App\User','user_id');
    }

}
