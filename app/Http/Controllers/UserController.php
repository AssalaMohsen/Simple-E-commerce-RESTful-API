<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function login(Request $request){
        $user = User::where('email',$request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response(["message"=>"These credentials does not match our records."],404);
        }
        $token = $user->createToken('my_app_token')->plainTextToken;
        $user = Auth::guard('web')->user();
        return response(["token_type" => "Bearer",
                        "access_token" => $token,]
                        ,200);
    }

    public function register(Request $request)
    {
        $this->validate($request, [
            'name' => ['required', 'string', 'min:3', 'max:255',],
            'email' => ['bail', 'required', 'email:strict', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        $user = new User();
        $user->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        $user->save();

        event(new \Illuminate\Auth\Events\Registered($user));

        return response()->noContent();
    }

    public function logout(Request $request)
    {
        $user = User::where('id',$request->user()->id)->first();

        $user->tokens()->delete();

        event(new \Illuminate\Auth\Events\Logout('sanctum', $user));

        return response()->noContent();
    }
}
