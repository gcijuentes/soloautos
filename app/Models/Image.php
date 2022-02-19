<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class image extends Model
{
    protected $table = 'imagen';
    protected $primaryKey = 'id';
    use HasFactory;

    protected $fillable = [
        'nombre_imagen',
        'tipo',
        'extension',
        'ruta',
    ];


    //relacion many to one
    public function car(){
        return $this->belongsTo('App\Models\Car','id_vehiculo','id');
    }

}
