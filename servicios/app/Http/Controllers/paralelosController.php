<?php

namespace App\Http\Controllers;
use App\Models\paralelo;
use App\Models\unidadEducativa;
use App\Models\detalleParalelo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;

class paralelosController extends Controller
{
    //pruebas
    public function pruebas(Request $request){
        $idParalelo=$request->input('idParalelo');
        $nombre=$request->input('nombre');
        $tipoDocumento=$request->input('tipoDocumento');

        $archivo = detalleParalelo::where([
            ['NOMBRE_ARCHIVO', '=', $nombre],
            ['ID_DOCUMENTO','=',$tipoDocumento],
            ['ID_PARALELO','=',$idParalelo]
            ])->get();
            return $archivo;
    }
        //obtener los paralelos por nivel
        public function getParalelos($idNivel){
            $paralelos = paralelo::where('ID_NIVEL','=',$idNivel)->get();
            return response()->json([
                'paralelos' => $paralelos,
                'HttpResponse' => [
                    
                    'message' => 'Paralelos Consultados',
                    'status' => 200,
                    'statusText' => 'OK',
                    'ok' => true
                ]
            ]);
        }
    //obtener los archivos por paralelos
    public function getArchivos($id){
        // $id=$request->input('id');
          $lectivos = detalleParalelo::where('ID_PARALELO', '=', $id)->get();
  
  
          if (!$lectivos) {
              return response()->json([
                  'HttpResponse' => [
                      'tittle' => 'Error',
                      'message' => 'No se cargo los archivos',
                      'status' => 400,
                      'statusText' => 'error',
                      'ok' => true
                  ]
              ]);
          }
  
  
          return response()->json([
              'archivos' => $lectivos,
              'HttpResponse' => [
                  
                  'message' => 'Archivos Consultados',
                  'status' => 200,
                  'statusText' => 'OK',
                  'ok' => true
              ]
          ]);
      }
    //////Archivos
    //subirArchivo
    public function subirArchivo(Request $request){
        //verifico si no hay otro archivo con ese nombre
        ///idUnidad / Documentos / idAnioLectivo / idCarpeta
        $idParalelo= $request->input('idParalelo');
        $tipoDocumento= $request->input('tipoDocumento');
        $nombre= $request->input('nombreArchivo');

        $archivo = detalleParalelo::where([
            ['ID_PARALELO', '=', $idParalelo],
            ['NOMBRE_ARCHIVO','=',$nombre],
            ['TIPO_DOCUMENTO','=',$tipoDocumento]])->get();  
        if(count($archivo)<1){
        $nombres = unidadEducativa::where('paralelo.ID','=',$idParalelo)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->join('nivel','nivel.ID_ANIO_LECTIVO','=','anio_lectivo.ID') 
        ->join('paralelo','paralelo.ID_NIVEL','=','nivel.ID')     
        ->select('unidad_educativa.NOMBRE as nombreUnidad','paralelo.NOMBRE as nombreParalelo','nivel.NOMBRE as nombreNivel', 'anio_lectivo.NOMBRE as nombreLectivo')->first();

        ///
        $archivo = new detalleParalelo();
        $archivo->id_paralelo = $request->input('idParalelo');
        $archivo->nombre_archivo = $request->input('nombreArchivo');
        $archivo->tipo_documento = $request->input('tipoDocumento');
        if($request->input('tipoDocumento')==1){
            $tipo="Boletas";
        }else if($request->input('tipoDocumento')==2){
            $tipo="Promociones";
        }else{
            $tipo="Concentrado";
        }
        //recivo y guardo el archivo
        $archivoFile = $request->file('archivo');
        $linkRoute= 'unidadesEducativas/'.$nombres->nombreUnidad.'/'.$nombres->nombreLectivo.'/'.$nombres->nombreNivel.'/'.$nombres->nombreParalelo.'/'.$tipo.'/';
        //$linkRoute= 'unidadesEducativas/pruebas/';
        $archivoguardado= $archivoFile->storeAs($linkRoute, $archivo->nombre_archivo, 'public');
        $archivo->formato = $request->input('formato');
        $archivo->save();

        return response()->json([
            'HttpResponse' => [
                
                'message' => 'archivoGuardado',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ]
        ]);
        }else{
            return response()->json([
                'HttpResponse' => [
                    'message' => 'Archivo con nombre ya existente!: '.$request->input('nombreArchivo'),
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }
    }
    //descargar elarchivo 
    public function descargarArchivo(Request $request){
        
            $idParalelo=$request->input('idParalelo');
            $formato=$request->input('formato');
            $nombre=$request->input('nombre');
            $tipoDocumento= $request->input('tipoDocumento');
            if($request->input('tipoDocumento')==1){
                $tipo="Boletas";
            }else if($request->input('tipoDocumento')==2){
                $tipo="Promociones";
            }else{
                $tipo="Concentrado";
            }
            ///GET para obtener los nombres de las carpetas
            $nombres = unidadEducativa::where('paralelo.ID','=',$request->input('idParalelo'))
            ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
            ->join('nivel','nivel.ID_ANIO_LECTIVO','=','anio_lectivo.ID') 
            ->join('paralelo','paralelo.ID_NIVEL','=','nivel.ID')     
            ->select('unidad_educativa.NOMBRE as nombreUnidad','paralelo.NOMBRE as nombreParalelo','nivel.NOMBRE as nombreNivel', 
            'anio_lectivo.NOMBRE as nombreLectivo')->first();
            $ruta= 'unidadesEducativas/'.$nombres->nombreUnidad.'/'.$nombres->nombreLectivo.'/'.$nombres->nombreNivel.'/'.$nombres->nombreParalelo.'/'.$tipo.'/'.$nombre;
            
           $file = storage_path()."\app\public/$ruta";
          $headers = [
           'Content-Type' => $formato,
          ];     
         return response()->download($file,$nombre,$headers);
    
     }
     //eliminar Archivo
     public function borrarArchivo(Request $request){
        $idParalelo=$request->input('idParalelo');
        $nombre=$request->input('nombre');
        $tipoDocumento=$request->input('tipoDocumento');
        if($request->input('tipoDocumento')==1){
            $tipo="Boletas";
        }else if($request->input('tipoDocumento')==2){
            $tipo="Promociones";
        }else{
            $tipo="Concentrado";
        }
        ///GET para obtener los nombres de las carpetas
         $nombres = unidadEducativa::where('paralelo.ID','=',$request->input('idParalelo'))
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->join('nivel','nivel.ID_ANIO_LECTIVO','=','anio_lectivo.ID') 
        ->join('paralelo','paralelo.ID_NIVEL','=','nivel.ID')     
        ->select('unidad_educativa.NOMBRE as nombreUnidad','paralelo.NOMBRE as nombreParalelo','nivel.NOMBRE as nombreNivel', 
        'anio_lectivo.NOMBRE as nombreLectivo')->first();

        $ruta= 'unidadesEducativas/'.$nombres->nombreUnidad.'/'.$nombres->nombreLectivo.'/'.$nombres->nombreNivel.'/'.$nombres->nombreParalelo.'/'.$tipo.'/'.$nombre;
        $archivo = detalleParalelo::where([
            ['NOMBRE_ARCHIVO', '=', $nombre],
            ['TIPO_DOCUMENTO','=',$tipoDocumento],
            ['ID_PARALELO','=',$idParalelo]
            ]);

            if (!$archivo) {
                return response()->json([
                    'HttpResponse' => [
                        'tittle' => 'Error',
                        'message' => 'No se encontro el archivo!',
                        'status' => 400,
                        'statusText' => 'error',
                        'ok' => true
                    ]
                ]);
            }
            try {
                Storage::disk('public')->delete($ruta);
                $archivo->delete();
    
                return response()->json([
                    'HttpResponse' => [
                        'tittle' => 'Correcto',
                        'message' => 'Archivo eliminado!',
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
