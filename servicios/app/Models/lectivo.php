<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class lectivo extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='anio_lectivo';
    protected $fillable = [
    'id',
    'nombre',
    'id_unidad_educativa'
    ];
}
