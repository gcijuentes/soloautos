<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    protected $table = 'provincia';
    protected $primaryKey = 'id';
    use HasFactory;

    protected $fillable = [
        'provincia_nombre'
    ];


    //relacion One to many
    public function ciudades(){
        return $this->hasMany('App\Models\City');
    }

    //relacion many to one
    public function region(){
        return $this->belongsTo('App\Models\Region','id_region','id');
    }

}
