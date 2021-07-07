<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalleParalelo extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='detalle_paralelo';
    protected $fillable = [
    'id_paralelo',
    'tipo_documento',
    'nombre_archivo',
    'formato'
    ];
}
