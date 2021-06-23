<?php

namespace App\Http\Controllers;
use App\Models\unidadEducativa;
use App\Models\lectivo;
use App\Models\detalleDocumentacion;
use App\Models\documentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class UEController extends Controller
{
    public function probarRespuesta(){
        return base_path();
      }
      ///
      public function prueba(Request $request){
       // Storage::disk('public')->makeDirectory('archNombres');
       //Storage::makeDirectory('public/archNombres3');
       Storage::deleteDirectory('public/archNombres3');
        return "borrado";
    }
    public function prueba2(Request $request){
        $idLectivo=$request->input('idLectivo');
        $nombres = unidadEducativa::where('anio_lectivo.ID','=',$idLectivo)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->select('unidad_educativa.NOMBRE as nombreUnidad','anio_lectivo.NOMBRE as nombreLectivo')
        ->first();
        return $nombres;
         
    }
      ///
    
    public function insertarUnidad(Request $request){
        $unidad = new unidadEducativa();
        $unidad->nombre= $request->input('nombre');
        $unidad->direccion= $request->input('direccion');
        $archivo=$request->file('archivo');
        $ruta=$archivo->storeAs('unidadesEducativas/logos', $unidad->nombre . '.' . $archivo->getClientOriginalExtension(), 'public');
        $unidad->ruta_logo=$ruta;
        $unidad->save();
        Storage::makeDirectory('public/unidadesEducativas/'.$nombres->nombreUnidad);
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
        function getImagenes($unidades){
            $array=[];
            foreach($unidades as $unidades){
               $object = (object)['ID' => $unidades->ID, 'URL' => asset('storage/'.$unidades->RUTA_LOGO),'NOMBRE' => $unidades->NOMBRE,
               'DIRECCION' => $unidades->DIRECCION];
               array_push($array, $object);
            }
            return $array;
        }
       $unidades = unidadEducativa::all();
        return response()->json(
            [
                //'unidades' => $unidades,
                'unidades' => getImagenes($unidades),
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
