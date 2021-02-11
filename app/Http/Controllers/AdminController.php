<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    public function login(Request $request){
        $admin = Admin::where('email',$request->email)->first();
        if(!$admin || !Hash::check($request->password, $admin->password)){
            return response(["message"=>"These credentials does not match our records."],404);
        }
        $token = $admin->createToken('my_app_token')->plainTextToken;
        $admin = Auth::guard('web')->user();
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

        $admin = new Admin();
        $admin->fill([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);
        $admin->save();

        event(new \Illuminate\Auth\Events\Registered($admin));

        return response()->noContent();
    }

    public function logout(Request $request)
    {
        $admin = Admin::where('id',$request->user()->id)->first();

        $admin->tokens()->delete();

        event(new \Illuminate\Auth\Events\Logout('sanctum', $admin));

        return response()->noContent();
    }
}
