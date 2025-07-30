<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\CatalogoController;
use App\Http\Controllers\DocumentoController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', [RegistrationController::class, 'create'])->name('registration.create');
Route::post('/consultar-cedula', [RegistrationController::class, 'consultarCedula'])->name('registration.consultarCedula');
Route::post('/consultar-cne', [RegistrationController::class, 'consultarCne'])
    ->name('registration.consultarCne');
Route::post('/guardar-registro', [RegistrationController::class, 'store'])
    ->name('registration.store');

//catalogs
Route::get('/catalogo/provincias', [CatalogoController::class, 'provincias']);
Route::get('/catalogo/cantones/{provincia_id}', [CatalogoController::class, 'cantones']);
Route::get('/catalogo/zonas', [CatalogoController::class, 'zonas']);

Route::get('/documentos/create', [DocumentoController::class, 'create'])->name('documentos.create');
Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');

Route::post('/documentos/upload', [DocumentoController::class, 'upload'])->name('documentos.upload');
