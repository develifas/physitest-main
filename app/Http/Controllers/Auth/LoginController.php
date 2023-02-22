<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Models\User;

class LoginController extends Controller
{
    public function login(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'login' => 'required',
            'password' => 'required',
            'token_name' => 'required',
        ]);

        $email =  User::select('email')
               ->where('email', $request->login)
               ->get();

        if(!$validator->fails()) {
            if(!auth()->attempt(['email' => $email, 'password' => $request->password]))
                return response()->json([
                    'message' => 'Login failed',
                    'errors' => 'Invalid credentials'
                ], 401);


            $user = User::select('*')
                  ->where('email', $request->login)
                  ->get();
            $token = $request->user()->createToken($request->token_name);
            return response()->json([
                'profile' => $user[0],
                'access_token' => $token->plainTextToken
            ], 200);
        } else {
            $errors = $validator->errors();

            return response()
                ->json([
                    'message' => 'Registration failed',
                    'errors' => $errors->all()
                ], 400);
        }
    }
}
