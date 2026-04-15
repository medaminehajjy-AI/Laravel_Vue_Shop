<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DebugController extends Controller
{
    public function csrfDebug(Request $request)
    {
        return response()->json([
            'session_id' => $request->session()->getId(),
            'csrf_token' => $request->session()->token(),
            'has_session' => $request->hasSession(),
            'cookies' => $request->cookies->all(),
            'headers' => [
                'x-xsrf-token' => $request->header('X-XSRF-TOKEN'),
                'origin' => $request->header('Origin'),
                'referer' => $request->header('Referer'),
            ],
        ]);
    }

    public function authDebug(Request $request)
    {
        return response()->json([
            'is_authenticated' => auth()->check(),
            'user' => auth()->user(),
            'session_id' => $request->hasSession() ? $request->session()->getId() : null,
        ]);
    }
}
