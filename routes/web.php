<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\TestLoginController;
use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;

Route::get('/', function () {
    return view('welcome');
});


Route::post('/login', [AuthController::class, 'login'])
    ->middleware(EnsureFrontendRequestsAreStateful::class);

Route::post('/register', [AuthController::class, 'register'])
    ->middleware(EnsureFrontendRequestsAreStateful::class);

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth:sanctum');

Route::post('/test-login', [TestLoginController::class, 'test'])
    ->middleware(EnsureFrontendRequestsAreStateful::class);

Route::get('/debug-request', function (\Illuminate\Http\Request $request) {
    return response()->json([
        'has_session' => $request->hasSession(),
        'session_id' => $request->hasSession() ? $request->session()->getId() : null,
        'csrf_token' => $request->hasSession() ? $request->session()->token() : null,
        'headers' => [
            'x-xsrf-token' => $request->header('X-XSRF-TOKEN'),
            'origin' => $request->header('Origin'),
            'referer' => $request->header('Referer'),
        ],
        'cookies' => $request->cookies->all(),
    ]);
});


