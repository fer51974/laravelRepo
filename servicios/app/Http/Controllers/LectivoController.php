<?php

namespace App\Http\Controllers;
use App\Models\lectivo;
use Illuminate\Http\Request;

class LectivoController extends Controller
{
    public function getLectivos($id){
      // $id=$request->input('id');
        $lectivos = lectivo::where('ID_UNIDAD_EDUCATIVA', '=', $id)->get();


        if (!$lectivos) {
            return response()->json([
                'HttpResponse' => [
                    'tittle' => 'Error',
                    'message' => 'No se cargo losc Años Lectivos',
                    'status' => 400,
                    'statusText' => 'error',
                    'ok' => true
                ]
            ]);
        }


        return response()->json([
            'lectivos' => $lectivos,
            'HttpResponse' => [
                
                'message' => 'Años lectivos Consultados',
                'status' => 200,
                'statusText' => 'OK',
                'ok' => true
            ]
        ]);
    }
}
