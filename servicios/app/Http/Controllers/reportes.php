<?php

namespace App\Http\Controllers;
use App\Models\documentacion;
use App\Models\detalleDocumentacion;
use App\Models\nivel;
use App\Models\paralelo;
use App\Models\detalleParalelo;

use Illuminate\Http\Request;
use Carbon\Carbon;
class reportes extends Controller
{
    //reporte de documentos de una unidad educativo en un periodo lectivo
    public function reporteDocumentos($idLectivo){
        
       // $idLectivo=$request->input('idlectivo');
        $carpetas=documentacion::where('ID_ANIO_LECTIVO', '=', $idLectivo)->get();
        //return $carpetas;
        
        function getDocs($carpetas){
            $array=[];
            $diaActual = Carbon::now()->locale('es')->translatedFormat('l d \d\e F \d\e\l Y');
            foreach($carpetas as $carpetas){
               $object = (object)['Fecha'=>$diaActual,'ID' => $carpetas->ID,'NOMBRE' => $carpetas->NOMBRE,
               'lectivo' => $carpetas->ID_ANIO_LECTIVO,'docs'=>detalleDocumentacion::where('ID_DOCUMENTACION', '=', $carpetas->ID)->get()];
               array_push($array, $object);
            }
            return $array;
        }
        return getDocs($carpetas);
       // $detalle=detalleDocumentacion::where('ID_DOCUMENTACION', '=', $idDoc->get();  
    }

    ///reporte de documentos de paralelos y estudiantes en un nivel 
    public function reporteNivel($idNivel){
        $paralelos = paralelo::where('ID_NIVEL', '=', $idNivel)->get();
        //return $paralelos;
        function getArchivos($paralelos){
            $array=[];
            $diaActual = Carbon::now()->locale('es')->translatedFormat('l d \d\e F \d\e\l Y');
            foreach($paralelos as $paralelos){
               $object = (object)['Fecha'=>$diaActual,'ID' => $paralelos->ID,'NOMBRE' => $paralelos->NOMBRE,
               'nivel' => $paralelos->ID_NIVEL,'archivos'=>detalleParalelo::where('ID_PARALELO', '=', $paralelos->ID)
               ->orderBy('TIPO_DOcUMENTO')->get()];
               array_push($array, $object);
            }
            return $array;
        }
        return getArchivos($paralelos);
    }
    ///reporte de documentos de estudiantes por paralelo
    public function reporteParalelo($idParalelo){
        $detalle = detalleParalelo::where('ID_PARALELO', '=', $idParalelo)
        ->orderBy('TIPO_DOCUMENTO')->get();
        $diaActual = Carbon::now()->locale('es')->translatedFormat('l d \d\e F \d\e\l Y');
        return response()->json(
            [
                'archivos' => $detalle,
                'fecha'=> $diaActual,
                'HttpResponse' => [
                    'status' => 200,
                    'statusText' => 'OK',
                    'ok' => true
                ]
            ],
        );
    }
}
