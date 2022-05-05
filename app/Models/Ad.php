<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    public $timestamps = false;
    protected $table = 'aviso';
    protected $primaryKey = 'id';

    protected $fillable = [
        'titulo'
    ];

    //relacion One to many
    public function Car(){
        return $this->belongsTo('App\Models\Car','id_vehiculo','id');
    }


}
