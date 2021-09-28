<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\noticia;
use Illuminate\Support\Facades\Storage;

class noticiasController extends Controller
{
    public function subirNoticia(Request $request){
        $noticia=new noticia();
        $noticia->titulo=$request->input('titulo');
        $noticia->descripcion=$request->input('descripcion');
        $imagen=$request->file('imagen');
        $noticia->imagen=$noticia->titulo.'.'.$imagen->getClientOriginalExtension();
        
        
        try {
            $noticia->save();
            $imagen->storeAs('noticias', $noticia->titulo . '.' . $imagen->getClientOriginalExtension(), 'public');
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 200,
                        'message'=>'Noticia ingresada Correctamente',
                        'statusText' => 'OK',
                        'ok' => true
                    ]
                ],
            );
        } catch (Exceptio $e) {
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 400,
                        'message'=>$e
                    ]
                ],
            );
        }
    }
    public function getNoticias(){
        function getImagenes($noticias){
            $array=[];
            foreach($noticias as $noticias){
               $object = (object)['ID' => $noticias->ID, 'URL' => asset('storage/noticias/'.$noticias->IMAGEN),'TITULO' => $noticias->TITULO,
               'DESCRIPCION' => $noticias->DESCRIPCION];
               array_push($array, $object);
            }
            return $array;
        }
       $noticias = noticia::all();
        return response()->json(
            [
                //'unidades' => $unidades,
                'noticias' => getImagenes($noticias),
                'HttpResponse' => [
                    'status' => 200,
                    'statusText' => 'OK',
                    'ok' => true
                ]
            ],
        );
    }

    public function eliminarNoticia($id){
        $noticia = noticia::where('ID','=',$id)->get();
        $objeto=noticia::where('ID','=',$id);
        try{
            $objeto->delete();
            Storage::disk('public')->delete('noticias/'.$noticia[0]->IMAGEN);
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 200,
                        'message'=>'Noticia eliminada Correctamente',
                        'statusText' => 'OK',
                        'ok' => true
                    ]
                ],
            );
        } catch (Exception $e) {
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 400,
                        'message'=>$e
                    ]
                ],
            );
        }
        
    }
}
