<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index()
    {
        return view('home');
    }

    public function me(Request $request)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(null, 204);
        }

        return response()->json($user->only(['id','nombre','rol']), 200);
    }
}
