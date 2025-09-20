<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class AiChatController extends Controller
{
    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'auth_key' => 'required|string',
            'uuid' => 'required|string',
            'content' => 'required|string',
            'status' => ['required', 'string', Rule::in([
                Post::STATUS_COMPLETED,
                Post::STATUS_GENERATE,
                Post::STATUS_REGENERATE,
                Post::STATUS_ERROR
            ])],
        ]);

        abort_if($validated['auth_key'] !== config('services.ai_chat.auth_key'), 401, 'Invalid auth key');

        $post = Post::where('uuid', $validated['uuid'])->firstOrFail();

        $post->update([
            'content' => $validated['content']
        ]);

        $post->updateStatus($validated['status']);

        return response()->json([
            'success' => true,
        ]);
    }
}
