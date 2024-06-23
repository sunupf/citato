<?php

namespace App\Wrappers;

use LucianoTonet\GroqPHP\Groq;


class GroqChat
{
    public $model;
    public $api;

    function __construct ($model = NULL) {
        if (is_null($model)) {
            $this->model = "llama3-70b-8192";
        } else {
            $this->model = $model;
        }
        $this->api = new Groq(config('app.groq'));
    }

    function chat ($params) {
        $data = [ 
            "model" => $this->model,
            "messages" => $params 
        ];

        return $this->api->chat()->completions()->create($data);
    }
}
