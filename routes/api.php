<?php

use App\Http\Controllers\Api\AiChatController;
use Illuminate\Support\Facades\Route;

Route::put('ai-chat', [AiChatController::class, 'update']);