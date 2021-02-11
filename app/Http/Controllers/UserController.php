<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function index(Request $request){
        $user = User::where('email',$request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)){
            return response(["message"=>"These credentials does not match our records."],404);
        }
        $token = $user->createToken('my_app_token')->plainTextToken;
        return response(["user"=>$user,"token"=>$token],200);
    }
}
