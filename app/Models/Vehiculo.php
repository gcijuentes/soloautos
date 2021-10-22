<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehiculo extends Model
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
        return $this->belongsTo('App\Models\Ciudad','id_ciudad','id');
    }


}
