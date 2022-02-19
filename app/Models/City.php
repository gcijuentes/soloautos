<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $table = 'comuna';
    protected $primaryKey = 'id';
    use HasFactory;

    protected $fillable = [
        'comuna_nombre'
    ];


    //relacion One to many
    public function vehiculos(){
        return $this->hasMany('App\Models\Vehiculo');
    }

    //relacion many to one
    public function province(){
        return $this->belongsTo('App\Models\Province','id_provincia','id');
    }

}
