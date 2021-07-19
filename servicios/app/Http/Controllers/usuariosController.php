<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\usuario;
use App\Models\tipoUsuario;
use Illuminate\Support\Facades\Hash;

class usuariosController extends Controller
{
    public function getUsuarios(){
      //  $usuarios=usuario::where();
         //obtengo los usuarios idd,nombre,email,tipo,unidadeducativa
         $usuarios = usuario::join('unidad_educativa','users.ID_UNIDAD_EDUCATIVA','=','unidad_educativa.ID')   
         ->join('tipo_usuario','users.TIPO_USUARIO','=','tipo_usuario.ID') 
        ->select('users.ID','users.Nombre as nombreUser','users.email as emailUser','tipo_usuario.NOMBRE as tipoUser','unidad_educativa.NOMBRE as unidadUser')->get();
        return $usuarios;
        return response()->json(
            [
                'usuarios' => getImagenes($unidades),
                'HttpResponse' => [
                    'status' => 200,
                    'statusText' => 'OK',
                    'ok' => true
                ]
            ],
        );
    }
    public function getRoles(){
        $roles = tipoUsuario::all();
        return $roles;
    }
    //ingresar un nuevo usuario
    public function insertarUsuario(Request $request){
        $usuario=new usuario();
        $usuario->nombre=$request->input('nombre');
        $usuario->email=$request->input('email');
        $usuario->password=Hash::make($request->input('password'));
       $usuario->tipo_usuario=$request->input('tipo_usuario');
       $usuario->id_unidad_educativa=$request->input('id_unidad_educativa');
        try {
            $usuario->save();
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 200,
                        'message'=>'Usuario ingresado Correctamente',
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
}
