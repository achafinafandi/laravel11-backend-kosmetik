<?php

// use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

use Illuminate\Auth\Middleware\Authenticate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware(Authenticate::using('sanctum'));

//Kategori
Route::apiResource('/kategori', App\Http\Controllers\Api\KategoriController::class);
Route::apiResource('/supplier', App\Http\Controllers\Api\SupplierController::class);
Route::apiResource('/lokasi', App\Http\Controllers\Api\LokasiController::class);
Route::apiResource('/barang', App\Http\Controllers\Api\BarangController::class);
Route::apiResource('/rak', App\Http\Controllers\Api\RakController::class);
Route::apiResource('/masuk', App\Http\Controllers\Api\MasukController::class);
Route::apiResource('/pemindahan', App\Http\Controllers\Api\PemindahanController::class);
Route::apiResource('/keluar', App\Http\Controllers\Api\KeluarController::class);
