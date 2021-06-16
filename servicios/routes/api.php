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

//años lectivos
Route::get('getLectivos/{id}',[LectivoController::class,"getLectivos"]);

//documentacion
Route::get('getDocs/{id}',[documentosController::class,"getDocumentacionbyLectivo"]);

//archivos
Route::post('subirArchivo',[documentosController::class,"subirArchivo"]);
Route::get('getArchivos/{id}',[documentosController::class,"getArchivosbyDoc"]);
Route::get('descargarArchivo',[documentosController::class,'descargarArchivo']);
Route::get('deleteArchivo',[documentosController::class,'eliminarArchivo']);



