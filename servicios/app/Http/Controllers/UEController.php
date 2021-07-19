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
      // Storage::deleteDirectory('public/archNombres3');
       // return "borrado";
       $idUnidad= $request->input('idUnidad');
       $nombres = unidadEducativa::where('ID','=',$idUnidad)
       ->select('unidad_educativa.NOMBRE as nombreUnidad')
       ->get();  
       return $nombres[0]->nombreUnidad;
       

    }
    public function prueba2(Request $request){
        $idUnidad=$request->input('idUnidad');
        $nombres = unidadEducativa::where('unidad_Educativa.ID','=',$idUnidad)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')     
        ->select('unidad_educativa.NOMBRE as nombreUnidad')->first();
        return $nombres->nombreUnidad;
         
    }
      ///
    ///insertar nueva unidad Educativa
    public function insertarUnidad(Request $request){
        $unidad = new unidadEducativa();
        $unidad->nombre= $request->input('nombre');
        $unidad->direccion= $request->input('direccion');
        $archivo=$request->file('archivo');
        //comprobar si existe otra unidad educativa con ese nombre
        $unidadExiste = unidadEducativa::where([
            ['NOMBRE','=',$request->input('nombre')]])->get();  
        if(count($unidadExiste)<1){
        //si no hay unidades con ese nombre inserto
        $ruta=$archivo->storeAs('unidadesEducativas/logos', $unidad->nombre . '.' . $archivo->getClientOriginalExtension(), 'public');
        $unidad->ruta_logo=$ruta;
        $unidad->save();
        Storage::makeDirectory('public/unidadesEducativas/'.$unidad->nombre);
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
            
        );
            }else{
                return response()->json([
                    'HttpResponse' => [
                        'message' => 'Unidad Educativa ya existente!: '.$request->input('nombre'),
                        'status' => 400,
                        'statusText' => 'error',
                        'ok' => true
                    ]
                ]);
            }

    }
    ///eliminar unidadEducativa
    public function eliminarUnidad($idUnidad){
        //$idUnidad= $request->input('idUnidad');
        //obtengo el objeto de la base para eliminar
        $unidad=unidadEducativa::where('ID','=',$idUnidad);
        /// obtengo el nombre de la unidad
        $nombres = unidadEducativa::where('ID','=',$idUnidad)
        ->select('unidad_educativa.NOMBRE as nombreUnidad')
        ->get();  
        ///verifico que la unidad educativa no tenga anios lectivos
        $count = lectivo::where('ID_Unidad_Educativa', '=', $idUnidad)->count();
        if($count>=1){
            return response()->json([
                'HttpResponse' => [
                    'tittle' => 'Error',
                    'message' => 'No puede eliminar Unidades Edicativas con Años Lectivos!',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }else{
            try {
                Storage::deleteDirectory('public/unidadesEducativas/'.$nombres[0]->nombreUnidad);
                //elimino el logo de la unidad educativa
                 $res=Storage::disk('public')->delete('unidadesEducativas/logos/'.$nombres[0]->nombreUnidad.'.png');
                 if($res!=1){
                    $res=Storage::disk('public')->delete('unidadesEducativas/logos/'.$nombres[0]->nombreUnidad.'.jpg');
                 }
                 $unidad->delete();
     
                 return response()->json([
                     'HttpResponse' => [
                         'tittle' => 'Correcto',
                         'message' => 'Unidad Educativa eliminada!',
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

    ///obtener lista de unidades educativas 
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
        );
    }

    public function getUnidad($id){
        $unidad=unidadEducativa::find($id);
        return $unidad;
    }
}
