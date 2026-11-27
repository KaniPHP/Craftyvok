<?php

namespace App\Http\Controllers;

use App\Models\ChatConversation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function start(Request $request): JsonResponse
    {
        $data = $request->validate([
            'item' => ['required', 'string', 'max:160'],
            'message' => ['required', 'string', 'max:2000'],
            'token' => ['nullable', 'string', 'size:64'],
        ]);

        $token = $data['token'] ?? Str::random(64);
        $tokenHash = hash('sha256', $token);
        $conversation = ChatConversation::query()
            ->where('token_hash', $tokenHash)
            ->first();

        if (! $conversation) {
            $token = Str::random(64);
            $conversation = ChatConversation::create([
                'token_hash' => hash('sha256', $token),
                'item_name' => $data['item'],
                'status' => 'open',
            ]);
            $conversation->messages()->create([
                'sender' => 'assistant',
                'body' => 'Thanks for your message! Our team will reply here shortly.',
            ]);
        } else {
            $conversation->update([
                'item_name' => $data['item'],
                'status' => 'open',
            ]);
        }

        $conversation->messages()->create([
            'sender' => 'visitor',
            'body' => $data['message'],
        ]);
        $conversation->update(['last_message_at' => now()]);

        return response()->json([
            'token' => $token,
            'conversationId' => $conversation->id,
            'messages' => $conversation->messages()->oldest()->get(['id', 'sender', 'body', 'created_at']),
        ]);
    }

    public function messages(Request $request, ChatConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);

        return response()->json([
            'messages' => $conversation->messages()->oldest()->get(['id', 'sender', 'body', 'created_at']),
        ]);
    }

    public function send(Request $request, ChatConversation $conversation): JsonResponse
    {
        $this->authorizeConversation($request, $conversation);
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
            'item' => ['nullable', 'string', 'max:160'],
        ]);

        $conversation->messages()->create([
            'sender' => 'visitor',
            'body' => $data['message'],
        ]);
        $conversation->update([
            'item_name' => $data['item'] ?? $conversation->item_name,
            'status' => 'open',
            'last_message_at' => now(),
        ]);

        return response()->json(['sent' => true]);
    }

    private function authorizeConversation(Request $request, ChatConversation $conversation): void
    {
        $token = $request->header('X-Chat-Token');

        abort_unless(
            is_string($token) && hash_equals($conversation->token_hash, hash('sha256', $token)),
            404,
        );
    }
}
