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
            'store_id' => 'array',
            'role'     =>'required|string',
        ]);

        $validation['password'] = bcrypt($validation['password']);
        $user = User::create($validation);
        $user->assignRole($validation['role']);
        if ($request->hasFile('store_id')) {
        foreach ($validation['store_id'] as $store_id) {
                $user->stores()->attach($store_id);
            }
        }
        if ($user){
            return response()->json([
                'message' => 'User successfully registered!',
                'name'  => $user->fname,
            ], 200);
        }
        return response()->json([
            'message' => "Error",
        ], 400);

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
            $user->getRoleNames();
            if ($user){
                return response()->json([
                    'message' => 'login was successful',
                    'name'  => $user->fname,
                    'token' => $token->plainTextToken,
                    'role'  => $user->getRoleNames(),
                ], 200);
            }
        }
        return response()->json([
            'message' => 'email or password is wrong',
        ], 400);
    }

    /**
     * logout user
     */
    public function logout (Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json([
            'message' => 'You have successfully logged out and the token was successfully deleted'
        ], 200);
    }
}
