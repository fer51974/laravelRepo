<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class detalleDocumentacion extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='detalle_documentacion';
    protected $fillable = [
    'id_documentacion',
    'nombre_archivo',
    'ruta_archivo',
    'formato'
    ];
}
