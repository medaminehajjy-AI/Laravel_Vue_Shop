<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TestLoginController extends Controller
{
    public function test(Request $request)
    {
        $email = $request->input('email');
        $password = $request->input('password');
        
        Log::info('Test Login Attempt', [
            'email' => $email,
            'password_length' => strlen($password ?? ''),
            'has_session' => $request->hasSession(),
        ]);
        
        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
        
        if (Auth::attempt($validated)) {
            $request->session()->regenerate();
            
            return response()->json([
                'success' => true,
                'message' => 'Login successful',
                'user' => Auth::user(),
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
        ], 401);
    }
}
