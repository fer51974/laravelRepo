<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class paralelo extends Model
{
    use HasFactory;
    public $timestamps = false;
    protected $table='paralelo';
    protected $fillable = [
    'id',
    'nombre',
    'id_nivel'
    ];
}
