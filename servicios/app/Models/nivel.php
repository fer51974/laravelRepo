<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class nivel extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='nivel';
    protected $fillable = [
    'id',
    'nombre',
    'id_anio_lectivo',
    'jornada'
    ];
}
