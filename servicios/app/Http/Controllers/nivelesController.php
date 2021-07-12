<?php

namespace App\Http\Controllers;
use App\Models\nivel;
use App\Models\paralelo;
use App\Models\unidadEducativa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class nivelesController extends Controller
{
    //obtener los niveles por anio lectivo
    public function getNiveles($idLectivo){
        $niveles = nivel::where('ID_ANIO_LECTIVO','=',$idLectivo)->get();
        return response()->json([
            'niveles' => $niveles,
            'HttpResponse' => [
                
                'message' => 'Años lectivos Consultados',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ]
        ]);
    }
    //insertar nivel
    public function insertarNivel(Request $request){
        $nivel = new nivel();
        //verifico si no hay un nivel con ese nombre en esa jornada
        $existe=nivel::where([
        ['NOMBRE', '=', $request->input('nombreNivel')],
        ['JORNADA','=',$request->input('jornada')]
        ])->get();
        if(count($existe)<1){
            //obtengo los nombres para las carpetas
            $nombres = unidadEducativa::where('anio_lectivo.ID','=',$request->input('idLectivo'))
            ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
            ->select('unidad_educativa.NOMBRE as nombreUnidad','anio_lectivo.NOMBRE as nombreLectivo')->first();

            $nivel->nombre=$request->input('nombreNivel');
            $nivel->id_anio_lectivo=$request->input('idLectivo');
            $nivel->jornada=$request->input('jornada');
            $nivel->save();

             //creo la carpeta en disco
             Storage::makeDirectory('public/unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$request->input('jornada').'_'.$request->input('nombreNivel'));

            return response()->json([
                'HttpResponse' => [    
                    'message' => 'Nivel Creado!',
                    'status' => 200,
                    'statusText' => 'OK',
                    'ok' => true
                ]
            ]);
        }else{
            return response()->json([
                'HttpResponse' => [
                    'message' => 'Nivel:'.$request->input('nombreNivel').' '.$request->input('jornada').' ya existe!',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }

    }
    //eliminar nivel
    public function borrarNivel($idNivel){
        //compruebo si el nivel tiene paralelos
        $count = paralelo::where('ID_NIVEL', '=', $idNivel)->count();
        if($count>=1){
            return response()->json([
                'HttpResponse' => [
                    'tittle' => 'Error',
                    'message' => 'No puede eliminar Niveles que contengan Paralelos!',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }else{
            try {
                //obtengo los nombres para las carpetas
                $nombres = unidadEducativa::where('nivel.ID','=',$idNivel)
                ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
                ->join('nivel','nivel.ID_ANIO_LECTIVO','=','anio_lectivo.ID')    
                ->select('unidad_educativa.NOMBRE as nombreUnidad','nivel.JORNADA as jornada','nivel.NOMBRE as nombreNivel',
                 'anio_lectivo.NOMBRE as nombreLectivo')->first();
                //obtengo el nivel y lo elimino
                $nivel = nivel::where('ID', '=', $idNivel);
                $nivel->delete();
                //elimino la carpeta en el disco
                Storage::deleteDirectory('public/unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$nombres->jornada.'_'.$nombres->nombreNivel);
                 $nivel->delete();
     
                 return response()->json([
                     'HttpResponse' => [
                         'tittle' => 'Correcto',
                         'message' => 'Nivel eliminado!',
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
    //editar nivel
    public function editarNivel(Request $request){
        $idNivel=$request->input('idNivel');
        $nombreNivel=$request->input('nombreNivel');
        //obtengo los nombres para las carpetas
         $nombres = unidadEducativa::where('nivel.ID','=',$idNivel)
        ->join('anio_lectivo','anio_lectivo.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
        ->join('nivel','nivel.ID_ANIO_LECTIVO','=','anio_lectivo.ID')    
        ->select('unidad_educativa.NOMBRE as nombreUnidad','nivel.JORNADA as jornada','nivel.NOMBRE as nombreNivel',
        'anio_lectivo.NOMBRE as nombreLectivo')->first();
        //comprobar si no existe otro nivel con ese nombre
        $count = nivel::where([
            ['NOMBRE','=',$nombreNivel],
            ['JORNADA','=',$nombres->jornada]])->count();  
            if($count>=1){
                return response()->json([
                    'HttpResponse' => [
                        'message' => 'No se puede asignar este nombre!',
                        'status' => 400,
                        'statusText' => 'error',
                        'ok' => true
                    ]
                ]);
            }else{
                 $nivel=nivel::where('ID','=',$idNivel)
                ->update(['NOMBRE'=>$nombreNivel]);

       
            Storage::move('public/unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$nombres->jornada.'_'.$nombres->nombreNivel,
            'public/unidadesEducativas/'.$nombres->nombreUnidad.'/BoletasConProm/'.$nombres->nombreLectivo.'/'.$nombres->jornada.'_'.$nombreNivel);



        if(!$nivel){
            return "no existe el nivel";
        }else{
            return response()->json(
                [
                    'HttpResponse' => [
                        'tittle' => 'Correcto',
                        'message' => 'Nivel Actualizado!',
                        'status' => 200,
                        'statusText' => 'success',
                        'ok' => true
                    ],
                ],
            );
        }
            }
    }
}
