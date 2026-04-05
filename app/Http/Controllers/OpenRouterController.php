<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OpenRouterController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENROUTER_API_KEY'),
            'HTTP-Referer' => 'http://localhost', 
            'X-Title' => 'My Laravel App', 
            'Content-Type' => 'application/json',
        ])->post('https://openrouter.ai/api/v1/chat/completions', [
            "model" => "google/gemma-4-26b-a4b-it",
            "messages" => [
                [
                    "role" => "user",
                    "content" => $message
                ]
            ]
        ]);

        if (!$response->successful()) {
            return $response->json();
        }

        $reply = $response['choices'][0]['message']['content'] ?? 'No response';

        return response()->json([
            'reply' => $reply
        ]);
    }
}
