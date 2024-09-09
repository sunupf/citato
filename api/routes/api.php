<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;

Route::prefix('v1')->middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/tokens', function (Request $request) {
        $token = auth()->user()->createToken($request->name);
     
        return response()->json([
            "status"=>'success',
            "data"=>[
                'token' => $token->plainTextToken
            ],
            "message"=>"Token generated"
        ]);
    });

    Route::post('/detect', [ChatController::class, 'detect'])->name('detect.word');
    Route::post('/detect/cac', [ChatController::class, 'cac'])->name('detect.cac');
});