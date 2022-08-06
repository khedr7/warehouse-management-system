<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**get all users information */


    public function profiles(){
        $users = User::all();

        return response()->json([
            'users' => $users,
        ], 200);
    }



    /**get  user profile information */


    public function myprofile(){
        $user= User::get()->where('id', 'like', Auth::id());
        if ($user){
            return response()->json([
                'user'       => $user
            ], 200);
        }
        else{
        return response()->json([
            'message' => 'Error',
        ], 404);
         }



    }
}
