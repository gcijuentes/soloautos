<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $table = 'region';
    protected $primaryKey = 'id';
    use HasFactory;

    protected $fillable = [
        'nombre_region'
    ];

    //relacion One to many
    public function ciudades(){
        return $this->hasMany('App\Models\Ciudad');
    }


}
