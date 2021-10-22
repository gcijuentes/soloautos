<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ciudad extends Model
{
    protected $table = 'ciudad';
    protected $primaryKey = 'id';
    use HasFactory;

    protected $fillable = [
        'nombreCiudad'
    ];


    //relacion One to many
    public function vehiculos(){
        return $this->hasMany('App\Models\Vehiculo');
    }

    //relacion many to one
    public function region(){
        return $this->belongsTo('App\Models\Region','id_region','id');
    }

}
