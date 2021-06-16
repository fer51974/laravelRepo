<?php

namespace App\Http\Controllers;
use App\Models\documentacion;
use App\Models\detalleDocumentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class documentosController extends Controller
{
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
///archivos
    public function subirArchivo(Request $request){
        $archivo = new detalleDocumentacion();
        $archivo->id_documentacion = $request->input('idDocumento');
        $archivo->nombre_archivo = $request->input('nombreArchivo');
        //recivo y guardo el archivo
        $archivoFile = $request->file('archivo');
        $archivo->ruta_archivo= $archivoFile->storeAs('archNombres', $archivo->nombre_archivo, 'public');
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

      public function eliminarArchivo(Request $request){
        $nombre=$request->input('nombre'); 
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
