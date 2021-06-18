<?php

namespace App\Http\Controllers;
use App\Models\documentacion;
use App\Models\detalleDocumentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class documentosController extends Controller
{
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
        $carpeta = new documentacion();
        $carpeta->id_anio_lectivo= $request->input('idLectivo');
        $carpeta->nombre= $request->input('nombre');
        $carpeta->save();
        return response()->json(
            [
                'Unidad Educativa' => $carpeta,
                'HttpResponse' => [
                    'tittle' => 'Correcto',
                    'message' => 'Nueva Carpeta de documentacion creada!',
                    'status' => 200,
                    'statusText' => 'success',
                    'ok' => true
                ],
            ],
            201
        );
    }
    //eliminar una Carpeta o doc
    public function eliminarCarpeta2($id){

        $carpeta = documentacion::where('ID', '=', $id);
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
                // Storage::disk('public')->delete($ruta);
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
    public function eliminarCarpeta($id){
        $carpeta=documentacion::find($id);
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
            try{
                $carpeta->delete();
                return response()->json([
                    'HttpResponse' => [
                        'tittle' => 'Correcto',
                        'message' => 'Careta Eliminada!',
                        'status' => 200,
                        'statusText' => 'success',
                        'ok' => true
                    ],
                ]);
            }catch(Exception $e) {
                return $e;
            }
           
        }
    }
/////archivos
    public function subirArchivo(Request $request){
        $archivo = new detalleDocumentacion();
        $archivo->id_documentacion = $request->input('idDocumento');
        $archivo->nombre_archivo = $request->input('nombreArchivo');
        //recivo y guardo el archivo
        $archivoFile = $request->file('archivo');
        $linkRoute= $request->input('linkRoute');
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
    //descargar elarchivo con la ruta
    public function descargarArchivo(Request $request){
        $ruta=$request->input('ruta');
        $formato=$request->input('formato');
        $nombre=$request->input('nombre');
        $file = storage_path()."\app\public/$ruta";
        $headers = [
          'Content-Type' => $formato,
         ];     
       return response()->download($file,$nombre,$headers);
    }

    ///Eliminar Archivo con la ruta
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
