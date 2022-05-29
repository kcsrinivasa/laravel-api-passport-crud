<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{
    /**  For registration */

    public function register(Request $request){
        /* validate request*/
        $request->validate([
            'name'=>'required|string|regex:/[a-zA-Z]/|max:255',
            'email'=>'required|string|email|regex:/^([a-z0-9\+_\-]+)(\.[a-z0-9\+_\-]+)*@([a-z0-9\-]+\.)+[a-z]{2,6}$/ix|max:255|unique:users',
            'password'=>'required|string|min:8|confirmed',
        ],[
            'name.regex'=>'Please enter valid name',
            'email.regex'=>'Please enter valid email',
            'email.unique'=>'Entered email is already exists in our records',
        ]);

        /* create user */
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        /* genrate auth token*/
        $user->token = $user->createToken('LaravelAuthToken')->accessToken;

        $res = [
            'message' => 'User registered successfully',
            'user' => $user
        ];
        return response($res,200);
    }

    /**  For login */
    
    public function login(Request $request){
         /* validate request*/
        $request->validate([
            'email'=>'required|string|email',
            'password'=>'required|string',
        ]);
        $request = $request->only('email','password');

        if(auth()->attempt($request)){
            /* find user */
            $user = auth()->user();

            /* genrate auth token*/
            $user->token = $user->createToken('LaravelAuthToken')->accessToken;

            $res = [
                'message' => 'User logged in successfully',
                'user' => $user
            ];
            return response($res,200);
        }else{
            return response(['message'=>'Invalid email or password'],422);
        }
    }
}
