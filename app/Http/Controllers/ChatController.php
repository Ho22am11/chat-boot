<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\BootChat;
use Illuminate\Support\Str;

class ChatController extends Controller
{
    public function index()
    {
        $session_id = session()->getId();
        $history = BootChat::where('session_id', $session_id)->orderBy('created_at', 'asc')->get();
        return view('chat', compact('history'));
    }

    public function chat(Request $request)
    {
        $message = $request->input('message');
        $session_id = session()->getId();

        // 1. Get History (Last 10 messages)
        $history = BootChat::where('session_id', $session_id)
            ->latest()
            ->take(10)
            ->get()
            ->reverse();

        $contents = [];
        foreach ($history as $msg) {
            $contents[] = [
                "role" => $msg->role === 'user' ? 'user' : 'model',
                "parts" => [
                    ["text" => $msg->content]
                ]
            ];
        }

        // 2. Add current message with system instructions
        // We wrap the instructions only if it's the first message or prepend it to the current one
        $instruction = "System Instruction: User name is hossam. You are a helpful AI assistant. Use the previous history to provide relevant and coherent answers.\n\n";
        
        $contents[] = [
            "role" => "user",
            "parts" => [
                ["text" => $instruction . $message]
            ]
        ];

        // 3. Save User Message to DB
        BootChat::create([
            'session_id' => $session_id,
            'role' => 'user',
            'content' => $message,
        ]);

        // 4. API Request
        $response = Http::post(
            "https://generativelanguage.googleapis.com/v1beta/models/gemini-3-flash-preview:generateContent?key=" . env('GEMINI_API_KEY'),
            [
                "contents" => $contents
            ]
        );

        if (!$response->successful()) {
            return $response->json();
        }

        $reply = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';

        // 5. Save AI Reply to DB
        BootChat::create([
            'session_id' => $session_id,
            'role' => 'ai',
            'content' => $reply,
        ]);

        return response()->json([
            'reply' => $reply
        ]);
    }




    public function listAvailableModels()
{
    $response = Http::get("https://generativelanguage.googleapis.com/v1beta/models?key=" . env('GEMINI_API_KEY'));

    if (!$response->successful()) {
        return $response->json();
    }

    $models = collect($response['models'])->map(function ($model) {
        return [
            'name' => $model['name'],
            'supportedGenerationMethods' => $model['supportedGenerationMethods'] ?? []
        ];
    })->filter(function ($model) {
        return in_array('generateContent', $model['supportedGenerationMethods']);
    })->values();

    return response()->json([
        'available_models_for_chat' => $models
    ]);
}


    
}
