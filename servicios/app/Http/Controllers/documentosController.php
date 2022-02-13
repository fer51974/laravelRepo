<?php

namespace App\Http\Controllers;
use App\Models\documentacion;
use App\Models\detalleDocumentacion;
use App\Models\lectivo;
use App\Models\unidadEducativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;



class documentosController extends Controller
{
    //cambiar nombre carpeta
    public function cambiar(){
        Storage::move("public/unidadesEducativas/Unidad Educativa Las Americas/Documentos/Periodo 2017-2018/pruebas2", 
                        "public/unidadesEducativas/Unidad Educativa Las Americas/Documentos/Periodo 2017-2018/pruebas2Cambiado");
        return "cambiado";
    }


/////documentos o carpetas
    //listar las carpetas o doc por año lectivo
    public function getDocumentacionbyLectivo($id){          
              $carpetas = documentacion::where('ID_ANIO_LECTIVO', '=', $id)->get();

              if (!$carpetas) {
                  return response()->json([
                      'HttpResponse' => [
                          'tittle' => 'Error',
                          'message' => 'No se cargo los documentos',
                          'status' => 400,
                          'statusText' => 'error',
                          'ok' => true
                      ]
                  ]);
              }
              return response()->json([
                'Carpetas' => $carpetas,
                'HttpResponse' => [
                    
                    'message' => 'Documentos Consultados',
                    'status' => 200,
                    'statusText' => 'OK',
                    'ok' => true
                ]
            ]);
    }
    //insertar una nueva carpeta o doc
    public function insertarCarpeta(Request $request){
        $idLectivo= $request->input('idLectivo');
        $nuevoNombre= $request->input('nombre');
        //obtener nombre de unidad y lectivo
        $nombres = unidadEducativa::where('anio_lectivo.ID','=',$idLectivo)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->select('unidad_educativa.NOMBRE as nombreUnidad','anio_lectivo.NOMBRE as nombreLectivo')
        ->first();
        



        $carpeta = documentacion::where([
            ['ID_ANIO_LECTIVO', '=', $idLectivo],
            ['NOMBRE','=',$nuevoNombre]])->get();  
         $nuevaCarpeta = new documentacion();
            if(count($carpeta)<1){
                $nuevaCarpeta->id_anio_lectivo= $request->input('idLectivo');
                $nuevaCarpeta->nombre= $request->input('nombre');
                $nuevaCarpeta->save();
                //creo la carpeta en disco
                Storage::makeDirectory('public/unidadesEducativas/'.$nombres->nombreUnidad.'/Documentos/'.$nombres->nombreLectivo.'/'.$request->input('nombre'));
                //
                return response()->json(
                    [
                        'Unidad Educativa' => $nuevaCarpeta,
                        'HttpResponse' => [
                            'tittle' => 'Correcto',
                            'message' => 'Nueva Carpeta de documentacion creada!',
                            'status' => 200,
                            'statusText' => 'success',
                            'ok' => true
                        ],
                    ],
                  
                );
            }else{
                return response()->json([
                    'HttpResponse' => [
                        'message' => 'Carpeta con nombre ya existente!: '.$request->input('nombre'),
                        'status' => 400,
                        'statusText' => 'error',
                        'ok' => true
                    ]
                ]);
            }

    }
    //editar el nombre de una carpeta
    public function editarCarpeta(Request $request){
        $idCarpeta= $request->input('idCarpeta');
        $idLectivo= $request->input('idLectivo');
        $nuevoNombre= $request->input('nombreCarpeta');
        ///comprobar si no existe otra carpeta con ese nombre
        $carpeta = documentacion::where([
            ['ID_ANIO_LECTIVO', '=', $idLectivo],
            ['NOMBRE','=',$nuevoNombre]])->get();  
            
        if(count($carpeta)<1){
                    ///
        $nombre = unidadEducativa::where('documentacion.ID','=',$idCarpeta)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->join('documentacion','documentacion.ID_ANIO_LECTIVO','=','anio_lectivo.ID')     
        ->select('unidad_educativa.NOMBRE as nombreUnidad','anio_lectivo.NOMBRE as nombreLectivo','documentacion.NOMBRE as nombreCarpeta')
        ->first();
        $carpeta=documentacion::where('ID','=',$idCarpeta)
        ->update(['NOMBRE'=>$nuevoNombre]);

       
            Storage::move('public/unidadesEducativas/'.$nombre->nombreUnidad.'/Documentos/'.$nombre->nombreLectivo.'/'.$nombre->nombreCarpeta, 
            'public/unidadesEducativas/'.$nombre->nombreUnidad.'/Documentos/'.$nombre->nombreLectivo.'/'.$nuevoNombre);



        if(!$carpeta){
            return "no existe la carpeta";
        }else{
            return response()->json(
                [
                    'HttpResponse' => [
                        'tittle' => 'Correcto',
                        'message' => 'Carpeta actualizada!',
                        'status' => 200,
                        'statusText' => 'success',
                        'ok' => true
                    ],
                ],
                201
            );
        }
        }else{
            return response()->json([
                'HttpResponse' => [
                    'message' => 'No se puede asignar este nombre! Ya existe',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }


    }
    //eliminar una Carpeta o doc
    public function eliminarCarpeta2(Request $request){
        $idLectivo= $request->input('idLectivo');
        $nombreCarpeta= $request->input('nombreCarpeta');
        $id=$request->input('idCarpeta');
        $carpeta = documentacion::where('ID', '=', $id);

         //obtener nombre de unidad y lectivo
        $nombres = unidadEducativa::where('anio_lectivo.ID','=',$idLectivo)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->select('unidad_educativa.NOMBRE as nombreUnidad','anio_lectivo.NOMBRE as nombreLectivo')
        ->first();

        if (!$carpeta) {
            return response()->json([
                'HttpResponse' => [
                    'tittle' => 'Error',
                    'message' => 'No se encontro la carpeta!',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }
        $count = detalleDocumentacion::where('ID_DOCUMENTACION', '=', $id)->count();

        if($count>=1){
            return response()->json([
                'HttpResponse' => [
                    'tittle' => 'Error',
                    'message' => 'No puede eliminar Carpetas con Archivos!',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }else{
            try {
                Storage::deleteDirectory('public/unidadesEducativas/'.$nombres->nombreUnidad.'/Documentos/'.$nombres->nombreLectivo.'/'.$nombreCarpeta);
                 $carpeta->delete();
     
                 return response()->json([
                     'HttpResponse' => [
                         'tittle' => 'Correcto',
                         'message' => 'Carpeta eliminado!',
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

/////archivos
    public function subirArchivo(Request $request){
        //verifico si no hay otro archivo con ese nombre
        ///idUnidad / Documentos / idAnioLectivo / idCarpeta
        $idUnidad= $request->input('idUnidad');
        $idLectivo= $request->input('idLectivo');
        $idCarpeta= $request->input('idCarpeta');
        $nombre= $request->input('nombreArchivo');

        $archivo = detalleDocumentacion::where([
            ['ID_DOCUMENTACION', '=', $idCarpeta],
            ['NOMBRE_ARCHIVO','=',$nombre]])->get();  
        if(count($archivo)<1){
        //get el nombre de la unidad
        $unidad = unidadEducativa::where('id','=',$idUnidad)->get();
        //GET el nombre del anio lectivo
        $lectivo= lectivo::where('id','=',$idLectivo)->get();
        //GET el nombre de la carpeta
        $carpeta= documentacion::where('id','=',$idCarpeta)->get();
        ///comprobar nombre del archivo con el id de la carpeta

        ///
        $archivo = new detalleDocumentacion();
        $archivo->id_documentacion = $request->input('idCarpeta');
        $archivo->nombre_archivo = $request->input('nombreArchivo');
        //recivo y guardo el archivo
        $archivoFile = $request->file('archivo');
        $linkRoute= 'unidadesEducativas/'.$unidad[0]->NOMBRE.'/'.'Documentos/'.$lectivo[0]->NOMBRE.'/'.$carpeta[0]->NOMBRE.'/';
        $archivo->ruta_archivo= $archivoFile->storeAs($linkRoute, $archivo->nombre_archivo, 'public');
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


    //listar los archivos por el iddela carpeta o doc
    public function getArchivosbyDoc($id){            
        $archivos = detalleDocumentacion::where('ID_DOCUMENTACION', '=', $id)->get();

        if (!$archivos) {
            return response()->json([
                'HttpResponse' => [
                    'tittle' => 'Error',
                    'message' => 'No se cargo los documentos',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }
        return response()->json([
          'Archivos' => $archivos,
          'HttpResponse' => [     
              'message' => 'Documentos Consultados',
              'status' => 200,
              'statusText' => 'OK',
              'ok' => true
          ]
      ]);
    }
    //descargar elarchivo 
    public function descargarArchivo(Request $request){

        $idCarpeta=$request->input('idCarpeta');
        $formato=$request->input('formato');
        $nombre=$request->input('nombre');
        ///GET para obtener los nombres de las carpetas
        $nombres = unidadEducativa::where('detalle_documentacion.ID_DOCUMENTACION','=',$idCarpeta)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->join('documentacion','documentacion.ID_ANIO_LECTIVO','=','anio_lectivo.ID')     
        ->join('detalle_documentacion','detalle_documentacion.ID_DOCUMENTACION','=','documentacion.ID')
        ->select('unidad_educativa.NOMBRE as nombreUnidad','anio_lectivo.NOMBRE as nombreLectivo','documentacion.NOMBRE as nombreCarpeta')
        ->first();
        
         $ruta='unidadesEducativas/'.$nombres->nombreUnidad.'/Documentos/'.$nombres->nombreLectivo.'/'.$nombres->nombreCarpeta.'/'.$nombre;

       $file = storage_path()."\app\public/$ruta";
      $headers = [
       'Content-Type' => $formato,
      ];     
     return response()->download($file,$nombre,$headers);

    }

    ///Eliminar Archivo con la ruta
    public function eliminarArchivo2(Request $request){
        $idCarpeta=$request->input('idCarpeta');
        $nombre=$request->input('nombre');
        ///GET para obtener los nombres de las carpetas
        $nombres = unidadEducativa::where('detalle_documentacion.ID_DOCUMENTACION','=',$idCarpeta)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->join('documentacion','documentacion.ID_ANIO_LECTIVO','=','anio_lectivo.ID')     
        ->join('detalle_documentacion','detalle_documentacion.ID_DOCUMENTACION','=','documentacion.ID')
        ->select('unidad_educativa.NOMBRE as nombreUnidad','anio_lectivo.NOMBRE as nombreLectivo','documentacion.NOMBRE as nombreCarpeta')
        ->first();
        
        $ruta='unidadesEducativas/'.$nombres->nombreUnidad.'/Documentos/'.$nombres->nombreLectivo.'/'.$nombres->nombreCarpeta.'/'.$nombre;
       // return $ruta;
        $idCarpeta=$request->input('idCarpeta');
        $archivo = detalleDocumentacion::where([
            ['NOMBRE_ARCHIVO', '=', $nombre],
            ['ID_DOCUMENTACION','=',$idCarpeta],
            ]);
        //return $archivo;
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
    public function eliminarArchivo(Request $request){
        $ruta=$request->input('ruta'); 
        $archivo = detalleDocumentacion::where('RUTA_ARCHIVO', '=', $ruta);

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
