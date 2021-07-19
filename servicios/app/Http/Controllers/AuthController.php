<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\usuario;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Illuminate\Support\Facades\Hash;
class AuthController extends Controller
{
///usuarios

    public function getUsuarios(){
        $usuarios=usuario::where();
         //obtengo los nombres para las carpetas
         $usuarios = usuario::join('unidad_educativa','unidad_educativa.ID','=','users.ID_UNIDAD_EDUCATIVA')   
         ->join('tipo_usuario','tipo_usuario.ID','=','users.TIPO_USUARIO') 
        ->select('users.Nombre as nombreUser','users.email as emailUser','tipo_usuario.NOMBRE as tipoUser','unidad_educativa.NOMBRE as unidadUser')->all();
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
    public function register(Request $request){
        $usuario=new usuario();
        $usuario->nombre=$request->input('nombre');
        $usuario->email=$request->input('email');
        $usuario->password=Hash::make($request->input('password'));
       $usuario->tipo_usuario=$request->input('tipo_usuario');
       $usuario->id_unidad_educativa=$request->input('id_unidad_educativa');

        $usuario->save();

        return "se ha guardado el usuario";

    }
      /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }
        /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(
                [
                    'HttpResponse' => [
                        'tittle' => 'ERROR de Inicio de Sesion',
                        'message' => 'Datos de inicio Incorrectos',
                        'status' => 401,
                        'statusText' => 'success',
                        'ok' => true
                    ],
                ],
               
            );
        }
        return response()->json(
            [
                'token' => $token,
                'user' => Auth::user(),
                'HttpResponse' => [
                    'tittle' => 'Iniciando Sesión',
                    'message' => 'Ingreso Exitoso',
                    'status' => 201,
                    'statusText' => 'success',
                    'ok' => true
                ],
            ],
           
        );

      //  return $this->respondWithToken($token);
    }
        /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
        /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function authenticate(Request $request)
        {
            $credentials = $request->only('email','password');
            try {
                if (! $token = JWTAuth::attempt($credentials)) {
                    //return response()->json(['error' => 'invalid_credentials'], 401);

                    return response()->json(
                        [
                           
                            'token' => null,
                            'user' => null,
                            'HttpResponse' => [
                                'tittle' => 'Error de Inicio de Sesión',
                                'message' => 'Credenciales Incorrectas',
                                'status' => 401,
                                'statusText' => 'error',
                                'ok' => true
                            ],
                        ],
                        201
                    );
                }
            } catch (JWTException $e) {
                //return response()->json(['error' => 'could_not_create_token'], 500);
                return response()->json(
                    [
                       
                        'token' => null,
                    'user' => null,
                        'HttpResponse' => [
                            'tittle' => 'Error de Inicio de Sesión',
                            'message' => 'No se puede crear el Token',
                            'status' => 500,
                            'statusText' => 'error',
                            'ok' => true
                        ],
                    ],
                    201
                );
            }
            return response()->json(
                [
                    'token' => (compact('token')),
                    'user' => Auth::user(),
                    'HttpResponse' => [
                        'tittle' => 'Iniciando Sesión',
                        'message' => 'Ingreso Exitoso',
                        'status' => 401,
                        'statusText' => 'success',
                        'ok' => true
                    ],
                ],
                201
            );
           
        }


}
