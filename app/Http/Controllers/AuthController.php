<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;

class AuthController extends Controller
{
    public function newStore(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'device_name' => 'string'
        ]);
        $user = User::create([
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return Response::json([
            'user' => $user,
            'message' => 'user created'
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
            'device_name' => 'string'
        ]);
        $user = User::where('email', $request->email)->first();
        if ($user && Hash::check($request->password, $user->password)) {
            $device_name = $request->post('device_name', $request->userAgent());
            $token = $user->createToken($device_name);

            return Response::json([
                'token' => $token->plainTextToken,
                'user' => $user,
            ]);
        }
        return Response::json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function logout(Request $request){
        Auth::guard('web')->logout();

        $request->user()->currentAccessToken()->delete();


        return Response::json([
            "message" => 'user logout   '
        ]);
    }
}
