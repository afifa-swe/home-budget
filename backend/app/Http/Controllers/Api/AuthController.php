<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $data = $request->only(['name','email','password','password_confirmation']);
        $v = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|confirmed|min:6',
        ]);

        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        // login and return tokens
        return $this->issueToken($request->merge([ 'username' => $data['email'], 'password' => $data['password'] ]));
    }

    public function login(Request $request)
    {
        $data = $request->only(['email','password']);
        $v = Validator::make($data, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
        if ($v->fails()) {
            return response()->json(['errors' => $v->errors()], 422);
        }

        return $this->issueToken($request->merge([ 'username' => $data['email'], 'password' => $data['password'] ]));
    }

    protected function issueToken(Request $request)
    {
        // use client credentials from config (set via .env)
        $clientId = config('services.passport.password_client_id');
        $clientSecret = config('services.passport.password_client_secret');

        if (! $clientId || ! $clientSecret) {
            return response()->json(['message' => 'Password grant client not configured in env'], 500);
        }

        $params = [
            'grant_type' => 'password',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'username' => $request->input('username'),
            'password' => $request->input('password'),
            'scope' => '',
        ];

        $tokenRequest = Request::create('/oauth/token', 'POST', $params);
        $response = app()->handle($tokenRequest);

        return $response;
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
