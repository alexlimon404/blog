<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\Api\PushSubscriptionController;
use App\Http\Controllers\FirebaseConfigController;

Route::get('/', [BlogController::class, 'index']);
Route::get('/post/{slug}', [BlogController::class, 'show'])->name('blog.post');
Route::get('/category/{slug}', [BlogController::class, 'category'])->name('blog.category');
Route::get('/tag/{slug}', [BlogController::class, 'tag'])->name('blog.tag');
Route::get('/author/{id}', [BlogController::class, 'author'])->name('blog.author');

// Firebase config endpoint
Route::get('/firebase-config', [FirebaseConfigController::class, 'config']);

// API routes for push subscriptions
Route::prefix('api')->group(function () {
    Route::post('/push-subscriptions', [PushSubscriptionController::class, 'store']);
    Route::delete('/push-subscriptions', [PushSubscriptionController::class, 'destroy']);
});
