<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $table = 'marca';
    protected $primaryKey = 'id';
    use HasFactory;

    protected $fillable = [
        'nombre_marca'
    ];



}
