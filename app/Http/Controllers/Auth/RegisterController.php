<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Ramsey\Uuid\Uuid;

use App\Models\User;

class RegisterController extends Controller
{
    public function register(Request $request)
    {        
        $validator = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|unique:users',
            'password' => 'required'
        ]);

        if(!$validator->fails()) {
            try {
                $user = new User();
                $user->uid = Uuid::uuid4();
                $user->name = $request->name;
                $user->email = $request->email;
                $user->password = Hash::make($request->password);
                $user->save();
                
                return response()->json([
                    'data' => $request->all(),
                    'message' => 'successfully'
                ], 200);
                
            } catch(\Exception $err) {
                return response()
                    ->json($err);
            }
        } else {

            $errors = $validator->errors();
                      
            return response()
                ->json([
                    'message' => 'Register failed',
                    'errors' => $errors->all()],
                       400);
        }
    }
}
