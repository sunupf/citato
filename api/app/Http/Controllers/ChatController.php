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

        $prompt = "
        Rule :
        1. Kamu seorang Ahli bahasa Indonesia dan dapat mendeteksi kata umpatan dan kotor dalam bahasa indonesia
        2. Kamu bekerja dalam 3 tahap, 
        - thought : berfikir untuk menjawab pertanyaan berdasarkan konteks percakapan yang ada
        - action: melaksanakan tugas pendeteksian kata kotor dan umpatan kemudian menuliskan kata kata yang terdeteksi.
        - reflection: berfikir kembali untuk tiap-tiap kata yang terdeteksi.  apakah benar merupakan umpatan, kata kotor atau hanya sekedar ketidakpuasan atau sebal. mari pikirkan dengan seksama
        
        kembalikan jawaban dengan format json seperti contoh dibawah ini
        {
            detected: true,
            message: 'omong kosong, lama banget'
            position: [
                {
                    word:'omong kosong',
                    type:'umpatan',
                    confidence_level:0.9,
                    start:0,
                    end:11
                }
            ]
        }
        
        dan seperti contoh ini ketika hasil deteksi kosong
        {
            detected: false,
            message:'Iya, bener begitu',
            position: null
        }
        
        3. tuliskan posisi huruf ke berapa sampai berapa dalam kalimat tersebut pada start dan end. 
        4. tuliskan juga confidence level untuk tiap kata

        
        Apakah kalimat " . $request->message . " mengandung kata kasar atau kotor bedasarkan konteks kalimat diatas?. cukup return json saja";

        $message = [
            [
                'role' => 'system',
                'content' => $prompt
            ]
        ];

        Log::info($message);

        $groq = new GroqChat();

        try {
            $chatCompletion = $groq->chat($message);
    
            return response()->json([
                "status" => "success",
                "data" => json_decode($chatCompletion['choices'][0]['message']['content']),
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
            Log::info($chatCompletion['choices'][0]['message']['content']);
            return response()->json([
                "status" => "success",
                "data" => json_decode($chatCompletion['choices'][0]['message']['content']),
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
        $conversation = "";

        foreach($data as $key => $message) {
            if (isset($data[$key + 1])) {
                $conversation = $conversation.$message['role'].': '.$message['content']."\n";
            } else {
                $lastIndex = $key;
            }
        }
        
        $prompt = "
        Rule :
        1. Kamu seorang Ahli bahasa Indonesia dan dapat mendeteksi kata umpatan dan kotor dalam bahasa indonesia
        2. Kamu bekerja dalam 3 tahap, 
        - thought : berfikir untuk menjawab pertanyaan berdasarkan konteks percakapan yang ada
        - action: melaksanakan tugas pendeteksian kata kotor dan umpatan kemudian menuliskan kata kata yang terdeteksi.
        - reflection: berfikir kembali untuk tiap-tiap kata yang terdeteksi.  apakah benar merupakan umpatan, kata kotor atau hanya sekedar ketidakpuasan atau sebal. mari pikirkan dengan seksama
        
        kembalikan jawaban dengan format json seperti contoh dibawah ini
        {
            detected: true,
            message: 'omong kosong, lama banget'
            position: [
                {
                    word:'omong kosong',
                    type:'umpatan',
                    confidence_level:0.9,
                    start:0,
                    end:11
                }
            ]
        }
        
        dan seperti contoh ini ketika hasil deteksi kosong
        {
            detected: false,
            message:'Iya, bener begitu',
            position: null
        }
        
        3. tuliskan posisi huruf ke berapa sampai berapa dalam kalimat tersebut pada start dan end. 
        4. tuliskan juga confidence level untuk tiap kata
        
        Konteks percakapan dapat dilihat pada text berikut:
        " . $conversation . "
        
        Apakah kalimat berikut ini mengandung kata kasar atau kotor bedasarkan konteks kalimat diatas?. cukup return json saja";
        

        $messages = [
            [
                'role' => 'system',
                'content' => $prompt
            ],
            [
                'role' => 'user',
                'content' =>  $data[$lastIndex]['role'] . ": " . $data[$lastIndex]['content'] .""
            ]
        ];

        Log::info($message);

        return $messages;

    }
}
