<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class OpenAiController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . env('OPENAI_API_KEY'),
            'Content-Type' => 'application/json',
        ])->post('https://api.openai.com/v1/responses', [
            "model" => "gpt-4.1-mini",
            "input" => $message
        ]);

        if (!$response->successful()) {
            return $response->json();
        }

        $reply = $response['output'][0]['content'][0]['text'] ?? 'No response';

        return response()->json([
            'reply' => $reply
        ]);
    }
}
