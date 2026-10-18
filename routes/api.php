<?php

declare(strict_types=1);

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', fn (Request $request): UserResource => new UserResource($request->user()))->middleware('auth:sanctum')->name('user.show');
