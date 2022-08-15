<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Throwable;

class AuthController extends Controller
{
    /**
     * User Authentication
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        // Form Validasyon
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        try {
            $user = User::whereUsername($request->input('username'))->firstOrFail();
        } catch (Throwable $exception) {
            return new Response([
                'status' => false,
                'msg' => 'User not found.'
            ]);
        }

        if (Auth::attempt($request->all(['username', 'password']))) {
            [$userId, $token] = explode('|', $user->createToken('bearer')->plainTextToken);

            return new Response([
                'status' => true,
                'msg' => 'Login successful.',
                'token' => $token
            ]);
        } else {
            return new Response([
                'status' => false,
                'msg' => 'Password is not correct.'
            ]);
        }
    }
}
