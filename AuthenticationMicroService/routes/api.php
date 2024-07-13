<?php

use App\Http\Controllers\AuthController;
use App\Http\Middleware\AuthenticateToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/', function () {
    return view('welcome');
})->middleware(AuthenticateToken::class);


Route::post('/register', [AuthController::class, 'register'])
    ->name('register');

Route::post('/login', [AuthController::class, 'login'])
    ->name('login');

Route::post('/refresh-token', [AuthController::class, 'refreshToken'])
    ->name('refreshToken')
    ->middleware(AuthenticateToken::class);



Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware(AuthenticateToken::class);

Route::get('/validate-user', [AuthController::class, 'validateUserByToken']);
Route::get('/get-user', [AuthController::class, 'getUserByToken']);





