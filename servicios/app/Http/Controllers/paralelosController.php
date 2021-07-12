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

        $idNivel=$request->input('idNivel');
         //obtengo los nombres para las carpetas
         $nombres = unidadEducativa::where('nivel.ID','=',$idNivel)
         ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
         ->join('nivel','nivel.ID_ANIO_LECTIVO','=','anio_lectivo.ID') 
         ->select('unidad_educativa.NOMBRE as nombreUnidad','nivel.JORNADA as jornada','nivel.NOMBRE as nombreNivel','anio_lectivo.NOMBRE as nombreLectivo')->first();

      return $nombres;
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
    //subir paralelo
    public function subirParalelo(Request $request){
        $idNivel=$request->input('idNivel');
        $nombreParalelo=$request->input('nombreParalelo');
         //obtengo los nombres para las carpetas
         $nombres = unidadEducativa::where('nivel.ID','=',$idNivel)
         ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
         ->join('nivel','nivel.ID_ANIO_LECTIVO','=','anio_lectivo.ID') 
         ->select('unidad_educativa.NOMBRE as nombreUnidad','nivel.JORNADA as jornada','nivel.NOMBRE as nombreNivel','anio_lectivo.NOMBRE as nombreLectivo')->first();

        //comprobar si no existe otro paralelo con ese nombre en el nivel
        $existe=paralelo::where([
            ['NOMBRE', '=', $nombreParalelo],
        ['ID_NIVEL','=',$idNivel]
        ])->get();
        if(count($existe)<1){
           
            $paralelo = new paralelo();
            $paralelo->nombre=$nombreParalelo;
            $paralelo->id_nivel=$idNivel;
            $paralelo->save();

            //creo la carpeta en disco
             Storage::makeDirectory('public/unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$nombres->jornada.'_'.$nombres->nombreNivel.'/'.$nombreParalelo);
            
            return response()->json([
                'HttpResponse' => [
                    
                    'message' => 'Paralelo Creado Correctamente',
                    'status' => 200,
                    'statusText' => 'OK',
                    'ok' => true
                ]
            ]);
        }else{
            return response()->json([
                'HttpResponse' => [
                    'message' => 'Paralelo con nombre ya existente!: '.$request->input('nombreParalelo'),
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }

    }
    //eliminar Paralelo
    public function borrarParalelo($id){
    //compruebo si el paralelo no tiene archivos
    $count = detalleParalelo::where('ID_PARALELO', '=', $id)->count();
    if($count>=1){
        return response()->json([
            'HttpResponse' => [
                'tittle' => 'Error',
                'message' => 'No puede eliminar Paralelos con Documentos!',
                'status' => 400,
                'statusText' => 'error',
                'ok' => true
            ]
        ]);
    }else{
        try {
            //obtengo los nombres para las carpetas
            $nombres = unidadEducativa::where('paralelo.ID','=',$id)
            ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
            ->join('nivel','nivel.ID_ANIO_LECTIVO','=','anio_lectivo.ID') 
            ->join('paralelo','paralelo.ID_NIVEL','=','nivel.ID')     
            ->select('unidad_educativa.NOMBRE as nombreUnidad','nivel.JORNADA as jornada','paralelo.NOMBRE as nombreParalelo','nivel.NOMBRE as nombreNivel', 'anio_lectivo.NOMBRE as nombreLectivo')->first();
            //obtengo el paralelo y lo elimino
            $paralelo = paralelo::where('ID', '=', $id);
            $paralelo->delete();
            //elimino la carpeta en el disco
            Storage::deleteDirectory('public/unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$nombres->jornada.'_'.$nombres->nombreNivel.'/'.$nombres->nombreParalelo);
             $paralelo->delete();
 
             return response()->json([
                 'HttpResponse' => [
                     'tittle' => 'Correcto',
                     'message' => 'Paralelo eliminado!',
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
    //editar paralelo
    public function editarParalelo(Request $request){
        $idParalelo=$request->input('idParalelo');
        $nombreParalelo=$request->input('nombreParalelo');
        //comprobar si no existe otro paralelo con ese nombre
        $existe = paralelo::where([
            ['NOMBRE','=',$nombreParalelo]])->get();  
            
        if(count($existe)<1){
        //obtengo los nombres para las carpetas
        $nombres = unidadEducativa::where('paralelo.ID','=',$idParalelo)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->join('nivel','nivel.ID_ANIO_LECTIVO','=','anio_lectivo.ID') 
        ->join('paralelo','paralelo.ID_NIVEL','=','nivel.ID')     
        ->select('unidad_educativa.NOMBRE as nombreUnidad','nivel.JORNADA as jornada','paralelo.NOMBRE as nombreParalelo','nivel.NOMBRE as nombreNivel',
         'anio_lectivo.NOMBRE as nombreLectivo')->first();

        $paralelo=paralelo::where('ID','=',$idParalelo)
        ->update(['NOMBRE'=>$nombreParalelo]);

       
            Storage::move('public/unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$nombres->jornada.'_'.$nombres->nombreNivel.'/'.$nombres->nombreParalelo,
            'public/unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$nombres->jornada.'_'.$nombres->nombreNivel.'/'.$nombreParalelo);



        if(!$paralelo){
            return "no existe el periodo";
        }else{
            return response()->json(
                [
                    'HttpResponse' => [
                        'tittle' => 'Correcto',
                        'message' => 'Paralelo Actualizado!',
                        'status' => 200,
                        'statusText' => 'success',
                        'ok' => true
                    ],
                ],
            );
        }
        }else{

            return response()->json([
                'HttpResponse' => [
                    'message' => 'No se puede asignar este nombre!',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }
    }

//////Archivos
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
        ->select('unidad_educativa.NOMBRE as nombreUnidad','nivel.JORNADA as jornada','paralelo.NOMBRE as nombreParalelo','nivel.NOMBRE as nombreNivel', 'anio_lectivo.NOMBRE as nombreLectivo')->first();

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
        $linkRoute= 'unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$nombres->jornada.'_'.$nombres->nombreNivel.'/'.$nombres->nombreParalelo.'/'.$tipo.'/';
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
            ->select('unidad_educativa.NOMBRE as nombreUnidad','nivel.JORNADA as jornada','paralelo.NOMBRE as nombreParalelo','nivel.NOMBRE as nombreNivel', 
            'anio_lectivo.NOMBRE as nombreLectivo')->first();
            $ruta= 'unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$nombres->jornada.'_'.$nombres->nombreNivel.'/'.$nombres->nombreParalelo.'/'.$tipo.'/'.$nombre;
            
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
        ->select('unidad_educativa.NOMBRE as nombreUnidad','nivel.JORNADA as jornada','paralelo.NOMBRE as nombreParalelo','nivel.NOMBRE as nombreNivel', 
        'anio_lectivo.NOMBRE as nombreLectivo')->first();

        $ruta= 'unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$nombres->jornada.'_'.$nombres->nombreNivel.'/'.$nombres->nombreParalelo.'/'.$tipo.'/'.$nombre;
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
