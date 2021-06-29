<?php

namespace App\Http\Controllers;
use App\Models\nivel;
use Illuminate\Http\Request;

class nivelesController extends Controller
{
    //obtener los niveles por anio lectivo
    public function getNiveles($idLectivo){
        $niveles = nivel::where('ID_ANIO_LECTIVO','=',$idLectivo)->get();
        return response()->json([
            'niveles' => $niveles,
            'HttpResponse' => [
                
                'message' => 'Años lectivos Consultados',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ]
        ]);
    }
    //insertar nivel
    public function insertarNivel(Request $request){
        $nivel = new nivel();
        $nivel->nombre=$request->input('nombreNivel');
        $nivel->id_anio_lectivo=$request->input('idLectivo');
        $nivel->jornada=$request->input('jornada');
        $nivel->save();
        return response()->json([
            'HttpResponse' => [    
                'message' => 'Nivel Creado!',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ]
        ]);
    }
}
