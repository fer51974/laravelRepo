<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UEController;
use App\Http\Controllers\LectivoController;
use App\Http\Controllers\documentosController;
use App\Http\Controllers\nivelesController;
use App\Http\Controllers\paralelosController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\usuariosController;
use App\Http\Controllers\noticiasController;
use App\Http\Controllers\reportes;
/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
////usuarios
Route::group([
    'prefix' => 'auth'
], function ($router) {

    Route::post('logout', [AuthController::class,"logout"]);
});

Route::get('getRoles', [usuariosController::class,"getRoles"]);
Route::post('insertarUsuario', [usuariosController::class,"insertarUsuario"]);
Route::get('getUsuarios', [usuariosController::class,"getUsuarios"]);
Route::post('editarUsuario',[usuariosController::class,"editarUsuario2"]);
Route::delete('eliminarUsuario/{id}',[usuariosController::class,"eliminarUsuario"]);

Route::post('ingresar', [AuthController::class,"login"]);
Route::post('register', [AuthController::class,"register"]);


///unidades educativas services
Route::get('probar',[UEController::class,"probarRespuesta"]);
Route::post('insertarUnidad',[UEController::class,"insertarUnidad"]);
Route::get('getUnidades',[UEController::class,"getUnidades"]);
Route::get('getUnidad/{id}',[UEController::class,"getUnidad"]);
Route::delete('eliminarUnidad/{id}',[UEController::class,"eliminarUnidad"]);
Route::post('editarUnidad',[UEController::class,"editarUnidad"]);
Route::post('probando',[UEController::class,"prueba2"]);


//años lectivos
Route::get('getLectivos/{id}',[LectivoController::class,"getLectivos"]);
Route::post('insertarLectivo',[LectivoController::class,"insertarLectivo"]);
Route::post('eliminarLectivo',[LectivoController::class,"eliminarLectivo"]);
Route::post('editarLectivo',[LectivoController::class,"editarLectivo"]);

//documentacion
Route::get('getDocs/{id}',[documentosController::class,"getDocumentacionbyLectivo"]);
Route::post('insertarCarpeta',[documentosController::class,"insertarCarpeta"]);
Route::post('eliminarCarpeta',[documentosController::class,"eliminarCarpeta2"]);
Route::put('cambiar',[documentosController::class,"cambiar"]);
Route::post('editarCarpeta',[documentosController::class,"editarCarpeta"]);

//archivos
Route::post('subirArchivo',[documentosController::class,"subirArchivo"]);
Route::get('getArchivos/{id}',[documentosController::class,"getArchivosbyDoc"]);
Route::get('descargarArchivo',[documentosController::class,'descargarArchivo']);
Route::post('borrarArchivo',[documentosController::class,'eliminarArchivo2']);

//niveles
Route::post('insertarNivel',[nivelesController::class,"insertarNivel"]);
Route::get('getNiveles/{id}',[nivelesController::class,"getNiveles"]);
Route::delete('borrarNivel/{id}',[nivelesController::class,"borrarNivel"]);
Route::post('editarNivel',[nivelesController::class,"editarNivel"]);

//PARALELOS
Route::get('getParalelos/{id}',[paralelosController::class,"getParalelos"]);
Route::post('subirParalelo',[paralelosController::class,"subirParalelo"]);
Route::delete('borrarParalelo/{id}',[paralelosController::class,"borrarParalelo"]);
Route::post('editarParalelo',[paralelosController::class,"editarParalelo"]);

    //paralelos archivos
Route::post('subirArchivoParalelo',[paralelosController::class,"subirArchivo"]);
Route::get('getArchivosParalelo/{id}',[paralelosController::class,"getArchivos"]);
Route::get('descargarArchivoParalelo',[paralelosController::class,'descargarArchivo']);
Route::delete('borrarArchivoParalelo',[paralelosController::class,'borrarArchivo']);

Route::post('pruebas',[paralelosController::class,"pruebas"]);

//noticias
Route::post('subirNoticia',[noticiasController::class,"subirNoticia"]);
Route::get('getNoticias',[noticiasController::class,"getNoticias"]);
Route::delete('eliminarNoticia/{id}',[noticiasController::class,"eliminarNoticia"]);

//reportes
Route::get('reporteDocumentos/{id}',[reportes::class,"reporteDocumentos"]);

