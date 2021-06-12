<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class documentacion extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='documentacion';
    protected $fillable = [
    'id',
    'nombre',
    'id_anio_lectivo'
    ];
}
