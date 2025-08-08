<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(Request $request)
    {
        // Logic for authenticating the user
        // This could include validating credentials, checking user status, etc.

        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = User::find(Auth::user()->id);

            if ($user->role == 'admin') {
                $token = $user->createToken('token')->plainTextToken;

                return response()->json([
                    'status' => 200,
                    'message' => 'Login successful',
                    'token' => $token,
                    'id' => $user->id,
                    'name' => $user->name,

                ]);
            } else {
                return response()->json([
                    'status' => 401,
                    'message' => 'You are not authorized to access admin panel!'
                ], 401);
            }
        } else {
            return response()->json(['message' => 'Either Email or Password is not correct!'], 401);
        }
    }
}
