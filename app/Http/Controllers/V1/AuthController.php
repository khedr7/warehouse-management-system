<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * create user
     */

    public function register(Request $request)
    {
        $validation = $request->validate([
            'fname'    => 'required',
            'lname'    => 'required',
            'email'    => 'required|email',
            'password' => 'required',
            'phone'    => 'required',
            'store_id' => 'exists:stores,id'
        ]);

        $validation['password'] = bcrypt($validation['password']);
        $user = User::create($validation);
        $token = $user->createToken('auth');
        if ($request->hasFile('store_id')) {
            $user->stores()->attach($validation['store_id']);
        }
            return [
                'message' => 'User successfully registered!',
                'data'    => [
                    'name'  => $user->fname,
                    'token' => $token->plainTextToken
                ]
            ];
    }

    /**
     * login user
     */

    public function login (Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $user->tokens()->delete();
            $token = $user->createToken('auth');
            return [
                'message' => 'login was successful',
                'data'    => [
                    'name'  => $user->fname,
                    'token' => $token->plainTextToken
                ]
            ];
        }

        return [
            'message' => 'email or password is wrong'
        ];
    }

    /**
     * logout user
     */
    public function logout (Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return [
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ];
    }
}
