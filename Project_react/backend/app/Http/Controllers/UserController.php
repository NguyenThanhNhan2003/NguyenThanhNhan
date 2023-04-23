<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    function register(Request $req)
    {
        $validator = Validator::make($req->all(), [
            'name' => ['required', 'string', 'min:5', 'max:20', 'regex:/^[a-zA-Z0-9]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'regex:/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/i'],
            'password' => ['required', 'string', 'min:8', 'max:20'],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = new User();
        $user->name = $req->input('name');
        $user->email = $req->input('email');
        $user->password = Hash::make($req->input('password'));
        $user->save();

        return $user;
    }

    function login(Request $req)
    {
        $credentials = $req->only('email', 'password');
        $user = User::where('email', $req->email)->first();
        if (!$user || !Hash::check($req->password, $user->password))
            if (Auth::attempt($credentials)) {
                // Authentication passed
                return redirect()->intended('/api/login');
            } else {
                // Authentication failed
                return back()->withErrors(['email' => 'Invalid credentials']);
            }
        return $user;
    }

    // function search($key)
    // {
    //     return $key;
    // }
}
