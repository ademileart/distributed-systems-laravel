<?php

use App\Http\Controllers\PostsController;
use App\Http\Middleware\UserAuthenticationInAuthenticationMicroService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::post('/create', [PostsController::class, 'create'])
    ->middleware(UserAuthenticationInAuthenticationMicroService::class);

Route::get('/get-user-posts', [PostsController::class, 'getUserPosts'])
    ->middleware(UserAuthenticationInAuthenticationMicroService::class);

Route::get('/delete/{post_id}', [PostsController::class, 'deletePost'])
    ->middleware(UserAuthenticationInAuthenticationMicroService::class);
