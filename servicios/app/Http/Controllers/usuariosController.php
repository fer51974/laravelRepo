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
    //actualizar usuario
    public function editarUsuario2(Request $request){
        $idUsuario=$request->input('idUsuario');
        $nombreUsuario=$request->input('nombreUsuario');
        $idUnidad=$request->input('idUnidad');
        $password=$request->input('password');
        $tipo_usuario=$request->input('tipo_usuario');
        try{
            //nombre
            if($nombreUsuario!=0){
                $usuario = usuario::where('ID',$idUsuario)
                ->update(['NOMBRE'=>$nombreUsuario]);
            }
            //unidad
            if($idUnidad!=0){
                $usuario = usuario::where('ID',$idUsuario)
                ->update(['ID_UNIDAD_EDUCATIVA'=>$idUnidad]);
            }
            //password
            if($password!=0){
                $usuario = usuario::where('ID',$idUsuario)
                ->update(['PASSWORD'=>Hash::make($request->input('password'))]);
            }
            //tipo usuario
            if($tipo_usuario!=0){
                $usuario = usuario::where('ID',$idUsuario)
                ->update(['TIPO_USUARIO'=>$tipo_usuario]);
            }
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 200,
                        'message'=>'Usuario actualizado Correctamente',
                        'statusText' => 'OK',
                        'ok' => true
                    ]
                ],
            );
        }catch(Exception $e){
            return $e;
        }

    }
    public function editarUsuario(Request $request){
        $idUsuario=$request->input('idUsuario');
        $nombreUsuario=$request->input('nombreUsuario');
        $idUnidad=$request->input('idUnidad');
        $password=Hash::make($request->input('password'));
        $tipo_usuario=$request->input('tipo_usuario');
        
        try{
            $usuario = usuario::where('ID',$idUsuario)
            ->update(['NOMBRE'=>$nombreUsuario,'ID_UNIDAD_EDUCATIVA'=>$idUnidad,
            'PASSWORD'=>$password,'TIPO_USUARIO'=>$tipo_usuario]);
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 200,
                        'message'=>'Usuario actualizado Correctamente',
                        'statusText' => 'OK',
                        'ok' => true
                    ]
                ],
            );
        }catch(Exception $e){
            return $e;
        }

    }
    
    //ingresar un nuevo usuario
    public function insertarUsuario(Request $request){
        $usuario=new usuario();
        $usuario->nombre=$request->input('nombre');
        $usuario->email=$request->input('email');
        $usuario->password=Hash::make($request->input('password'));
       $usuario->tipo_usuario=$request->input('tipo_usuario');
       $usuario->id_unidad_educativa=$request->input('id_unidad_educativa');
        $count=usuario::where('email', '=', $request->input('email'))->count();
        if($count==1){
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 400,
                        'message'=>"No puede asignar este Correo"
                    ]
                ],
            );
        }
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
    public function eliminarUsuario($id){
        $usuario=usuario::where('ID','=',$id)->get();
        $object=usuario::where('ID','=',$id);
        if($usuario[0]->TIPO_USUARIO==1){
            $count=usuario::where('TIPO_USUARIO', '=', 1)->count();
            //return $count;
        }
        if($usuario[0]->TIPO_USUARIO==1&&$count==1){
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 400,
                        'message'=>"No puede eliminar mas Administradores"
                    ]
                ],
            );
        }else{
            $object->delete();
            return response()->json(
                [
                    'HttpResponse' => [
                        'status' => 200,
                        'message'=>'Usuario eliminado Correctamente',
                        'statusText' => 'OK',
                        'ok' => true
                    ]
                ],
            );
        }
        return $usuario[0]->TIPO_USUARIO;
    }
}
