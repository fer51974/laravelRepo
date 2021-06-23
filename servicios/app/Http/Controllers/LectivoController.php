<?php

namespace App\Http\Controllers;
use App\Models\lectivo;
use App\Models\unidadEducativa;
use App\Models\documentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LectivoController extends Controller
{

//Años Lectivos
    //obtenerAños lectivosxidUnidadEducativa
    public function getLectivos($id){
      // $id=$request->input('id');
        $lectivos = lectivo::where('ID_UNIDAD_EDUCATIVA', '=', $id)->get();


        if (!$lectivos) {
            return response()->json([
                'HttpResponse' => [
                    'tittle' => 'Error',
                    'message' => 'No se cargo losc Años Lectivos',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }


        return response()->json([
            'lectivos' => $lectivos,
            'HttpResponse' => [
                
                'message' => 'Años lectivos Consultados',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ]
        ]);
    }
    //insertar un nuevo año lectivo
    public function insertarLectivo(Request $request){
        $idUnidad=$request->input('idUnidad');
        $nuevoNombre= $request->input('nombreLectivo');
        ///comprobar si hay un lectivo con ese nombre en la unidad eeducativa
        $lectivoExiste = lectivo::where([
            ['ID_UNIDAD_EDUCATIVA', '=', $idUnidad],
            ['NOMBRE','=',$nuevoNombre]])->get();  
        ///obtener el nombre de la unidad
        $nombres = unidadEducativa::where('unidad_Educativa.ID','=',$idUnidad)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')     
        ->select('unidad_educativa.NOMBRE as nombreUnidad')
        ->first();
        ///
        if(count($lectivoExiste)<1){
        $lectivo = new lectivo();
        $lectivo->nombre=$request->input('nombreLectivo');
        $lectivo->id_unidad_educativa=$request->input('idUnidad');
        $lectivo->save();
        //crear la carpeta en el disco
        Storage::makeDirectory('public/unidadesEducativas/'.$nombres->nombreUnidad.'/Documentos/'.$nuevoNombre);

        return response()->json([
            'HttpResponse' => [    
                'message' => 'Año lectivo Creado!',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ]
        ]);
        }else{
            return response()->json([
                'HttpResponse' => [
                    'message' => 'Ya existe un Año Lectivo con este Nombre!',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }
        
    }
    //eliminar anio lectivo o periodo
    public function eliminarLectivo(Request $request){
        $idLectivo= $request->input('idLectivo');
        //comrpobar si existe el periodo o lectivo
        $lectivo = lectivo::where('ID', '=', $idLectivo);
        if (!$lectivo) {
            return response()->json([
                'HttpResponse' => [
                    'tittle' => 'Error',
                    'message' => 'No se encontro el lectivo!',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }
        //obtener los nombres del lectivo y la unidadEducativa
        $nombres = unidadEducativa::where('anio_lectivo.ID','=',$idLectivo)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->select('unidad_educativa.NOMBRE as nombreUnidad','anio_lectivo.NOMBRE as nombreLectivo')
        ->first();
        ///comprobar si el anio lectivo tiene carpetas o documentos
        $count = documentacion::where('ID_ANIO_LECTIVO', '=', $idLectivo)->count();
        if($count>=1){
            return response()->json([
                'HttpResponse' => [
                    'tittle' => 'Error',
                    'message' => 'No puede eliminar Periodos con Documentos!',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }else{
            try {
                Storage::deleteDirectory('public/unidadesEducativas/'.$nombres->nombreUnidad.'/Documentos/'.$nombres->nombreLectivo);
                 $lectivo->delete();
     
                 return response()->json([
                     'HttpResponse' => [
                         'tittle' => 'Correcto',
                         'message' => 'Periodo eliminado!',
                         'status' => 200,
                         'statusText' => 'success',
                         'ok' => true
                     ],
                 ]);
             } catch (Exception $e) {
     
                 return response()->json([
                     'HttpResponse' => [
                         'tittle' => 'Error',
                         'message' => 'Algo salio mal, intende nuevamente!',
                         'status' => 400,
                         'statusText' => 'error',
                         'ok' => true
                     ]
                 ]);
             }
        }
        
    }
}
