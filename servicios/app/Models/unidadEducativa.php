<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class unidadEducativa extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='unidad_educativa';
    protected $fillable = [
    'id',
    'nombre',
    'direccion',
    'ruta_logo'
    ];
}
