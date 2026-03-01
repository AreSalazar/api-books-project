<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CategoryController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

//Rutas de autenticación
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum'); //Solo un usuario autenticado puede cerrar sesión

//Rutas de libros
//Usuarios generales pueden ver los libros
Route::get('/books', [BookController::class, 'index']);
Route::get('/books/{id}', [BookController::class, 'show']);

//Usuarios autenticados pueden crear/editar/eliminar
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/books', [BookController::class, 'store']);
    Route::put('/books/{id}', [BookController::class, 'update']);
    Route::delete('/books/{id}', [BookController::class, 'destroy']);
});

//Rutas de reviews
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/books/{book}/reviews', [ReviewController::class, 'index']);
    Route::post('/books/{book}/reviews', [ReviewController::class, 'store']);
    Route::put('/reviews/{id}', [ReviewController::class, 'update']);
    Route::delete('/reviews/{id}', [ReviewController::class, 'destroy']);
    Route::get('/books/{book}/reviews/average', [ReviewController::class, 'average']);
});

// Categorías generales
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::get('/categories/{id}', [CategoryController::class, 'show']);
    Route::put('/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
});

// Categorías de un libro específico
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/books/{id}/categories', [BookController::class, 'categories']);
    Route::post('/books/{id}/categories', [BookController::class, 'attachCategory']); //Agregar una categoría a este libro
});
