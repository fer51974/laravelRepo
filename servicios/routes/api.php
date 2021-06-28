<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UEController;
use App\Http\Controllers\LectivoController;
use App\Http\Controllers\documentosController;

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
///unidades educativas services
Route::get('probar',[UEController::class,"probarRespuesta"]);
Route::post('insertarUnidad',[UEController::class,"insertarUnidad"]);
Route::get('getUnidades',[UEController::class,"getUnidades"]);
Route::delete('eliminarUnidad/{id}',[UEController::class,"eliminarUnidad"]);
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




