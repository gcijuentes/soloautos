<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Ad extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
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
