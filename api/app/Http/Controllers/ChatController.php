<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Wrappers\GroqChat;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    // detect kalimat apakah mengandung kata yang perlu disensor?
    function detect(Request $request) {

        $validator = $request->validate([
            'message' => 'required'
        ]);

        $message = [
            [
                'role' => 'system',
                'content' => "Kamu adalah pekerja lembaga sensor yang bertugas untuk memfilter kata-kata umpatan dari suatu kalimat yang dituliskan oleh pengguna. Jawab dengan jawaban ya jika kalimat mengandung umpatan dan tidak jika tidak mengandung umpatan. jawab dalam bentuk lowercase"
            ],
            [
                'role' => 'user',
                'content' => $request->message
            ]
        ];

        Log::info(json_encode($message));

        $groq = new GroqChat();

        try {
            $chatCompletion = $groq->chat($message);
    
            return response()->json([
                "status" => "success",
                "data" => $chatCompletion,
                "message" => "Classification Done"
            ], 200);
        } catch (Groq\APIError $err) {
            return response()->json([
                "status" => "failed",
                "data" => $err,
                "message" => $err->name
            ], 200);
        }


    }

    // detect apakah suatu kalimat mengandung kata yang perlu disensor berdasarkan konteks percakapan
    function cac(Request $request) {
        $validator = $request->validate([
            'messages' => 'required|array',
            'messages.*.content' => 'required',
            'messages.*.role' => 'required'
        ]);

        $lastIndex = NULL;

        $messages = $this->buildCacPrompt($request->messages);

        Log::info(json_encode($messages));

        $groq = new GroqChat();

        try {
            $chatCompletion = $groq->chat($messages);
    
            return response()->json([
                "status" => "success",
                "data" => $chatCompletion,
                "message" => "CAC Detection Done"
            ], 200);
        } catch (Groq\APIError $err) {
            return response()->json([
                "status" => "failed",
                "data" => $err,
                "message" => $err->name
            ], 200);
        }
    }

    private function buildCacPrompt($data) {
        $conversation = "<|chat|>\n";

        foreach($data as $key => $message) {
            if (isset($data[$key + 1])) {
                $conversation = $conversation.$message['role'].': '.$message['content']."\n";
            } else {
                $lastIndex = $key;
            }
        }
        
        $conversation = $conversation."<|/chat|>\n\n";
        $conversation = $conversation."Tentukan kalimat percakapan berikutnya apakah mengandung makian berdasarkan konteks percakapan tersebut. Jawab dengan ya atau tidak menggunakan lowercase";


        $messages = [
            [
                'role' => 'system',
                'content' => $conversation
            ],
            [
                'role' => 'user',
                'content' =>  $data[$lastIndex]['role'] . " menuliskan kalimat \"" . $data[$lastIndex]['content'] ."\""
            ]
        ];

        return $messages;

    }
}
