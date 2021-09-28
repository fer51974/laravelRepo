<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class noticia extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='noticias';
    protected $fillable = [
    'id',
    'nombre',
    'descripcion',
    'imagen'
    ];
}
