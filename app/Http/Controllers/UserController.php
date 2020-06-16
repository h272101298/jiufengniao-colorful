<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    //
    public function login(Request $post)
    {
        $name = $post->username;
        $password = $post->password;
        if (Auth::attempt(['name'=>$name,'password'=>$password],true)){
            return jsonResponse([
                'msg'=>'ok'
            ]);
        }
        return jsonResponse([
            'msg'=>'error'
        ],422);
    }
}
