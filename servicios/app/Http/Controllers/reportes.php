<?php

namespace App\Http\Controllers;
use App\Models\documentacion;
use App\Models\detalleDocumentacion;

use Illuminate\Http\Request;

class reportes extends Controller
{
    public function reporteDocumentos($idLectivo){
       // $idLectivo=$request->input('idlectivo');
        $carpetas=documentacion::where('ID_ANIO_LECTIVO', '=', $idLectivo)->get();
        //return $carpetas;
        
        function getDocs($carpetas){
            $array=[];
            foreach($carpetas as $carpetas){
               $object = (object)['ID' => $carpetas->ID,'NOMBRE' => $carpetas->NOMBRE,
               'lectivo' => $carpetas->ID_ANIO_LECTIVO,'docs'=>detalleDocumentacion::where('ID_DOCUMENTACION', '=', $carpetas->ID)->get()];
               array_push($array, $object);
            }
            return $array;
        }
        return getDocs($carpetas);
       // $detalle=detalleDocumentacion::where('ID_DOCUMENTACION', '=', $idDoc->get();
        
        
    }
}
