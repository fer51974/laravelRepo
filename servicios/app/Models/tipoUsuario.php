<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class tipoUsuario extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='tipo_usuario';
    protected $fillable = [
    'id',
    'nombre'
    ];
}
