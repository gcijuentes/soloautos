<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $table = 'vehiculo';
    protected $primaryKey = 'id';
    use HasFactory;

    protected $fillable = [
        'kilometraje'
        ,'combustible'
        ,'transmision'
        ,'consumo'
        , 'valor'
        ,'comentario'
        ,'color'
    ];


    //relacion One to many
    public function ciudad(){
        return $this->belongsTo('App\Models\City','id_ciudad','id');
    }

    //relacion One to many
    public function images(){
        return $this->hasMany('App\Models\Image','id_vehiculo');
    }


    public function ad(){
        return $this->hasOne('App\Models\Ad','id_vehiculo');
    }
}
