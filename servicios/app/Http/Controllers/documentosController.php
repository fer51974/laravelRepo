<?php

namespace App\Http\Controllers;
use App\Models\documentacion;
use App\Models\detalleDocumentacion;
use Illuminate\Http\Request;

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
       $file = base_path()."/app/descarga (2).pdf";
       $headers = [
         'Content-Type' => 'application/pdf',
    ];
      //return base_path();
       //
       
       return response()->download($file,'descargado.pdf',$headers);
      //if(!$this->downloadFile(app_path()."/files/prueba.pdf")){
      //return redirect()->back();
      //}
      }
}
