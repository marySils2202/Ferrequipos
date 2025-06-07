<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{

    public function showLoginForm()
    {
        return view('login');
    }


    public function login(Request $request)
    {

        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        if (Auth::attempt($credentials)) {
            return redirect()->intended('/sistema');
        }
        return back()->withErrors([
            'username' => 'Las credenciales no son válidas.',
        ])->onlyInput('username');
    }
    public function logout(Request $request)
    {
        Auth::logout(); 
    $request->session()->invalidate(); 
    $request->session()->regenerateToken(); 

    return redirect()->route('login'); 
}
    }

