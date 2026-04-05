<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class ChatController extends Controller
{
    public function chat(Request $request)
    {
        $message = $request->input('message');

       $response = Http::post(
           "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . env('GEMINI_API_KEY'),    [
               "contents" => [        
            [
                "parts" => [
                    ["text" => $message]
                ]
            ]
            ]
            ]
        );
        
        if (!$response->successful()) {
            return $response->json();
        }
        

        $reply = $response['candidates'][0]['content']['parts'][0]['text'] ?? 'No response';

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
