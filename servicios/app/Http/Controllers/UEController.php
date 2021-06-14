<?php

namespace App\Http\Controllers;
use App\Models\unidadEducativa;
use Illuminate\Http\Request;

class UEController extends Controller
{
    public function probarRespuesta(){
        return base_path();
      }
    
    public function insertarUnidad(Request $request){
        $unidad = new unidadEducativa();
        $unidad->nombre= $request->input('nombre');
        $unidad->direccion= $request->input('direccion');
        $archivo=$request->file('archivo');
        $ruta=$archivo->storeAs('unidadesEducativas/logos', $unidad->nombre . '.' . $archivo->getClientOriginalExtension(), 'public');
        $unidad->ruta_logo=$ruta;
        $unidad->save();
        return response()->json(
            [
                'Unidad Educativa' => $unidad,
                'HttpResponse' => [
                    'tittle' => 'Correcto',
                    'message' => 'Nuevo Unidad Educativa creada!',
                    'status' => 200,
                    'statusText' => 'success',
                    'ok' => true
                ],
            ],
            201
        );
    }

    public function getUnidades(){
        function getImagenes($tareas){
            $array=[];
            foreach($tareas as $tareas){
               $object = (object)['ID' => $tareas->ID, 'URL' => asset('storage/'.$tareas->RUTA_LOGO),'NOMBRE' => $tareas->NOMBRE,
               'DIRECCION' => $tareas->DIRECCION];
               array_push($array, $object);
            }
            return $array;
        }
       $tareas = unidadEducativa::all();
        return response()->json(
            [
                //'unidades' => $tareas,
                'unidades' => getImagenes($tareas),
                'HttpResponse' => [
                    'status' => 200,
                    'statusText' => 'OK',
                    'ok' => true
                ]
            ],
            201
        );
    }
}
