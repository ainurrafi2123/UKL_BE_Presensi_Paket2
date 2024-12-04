<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class LoginController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
       
        $validator = Validator::make($request->all(), [
            'username'  => 'required|string',  
            'password'  => 'required|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $credentials = $request->only('username', 'password');

        if (!$token = auth()->guard('api')->attempt($credentials)) {
            return response()->json([
                'success' => false,
                'message' => 'Username atau Password Anda salah'
            ], 401);
        }

        $user = auth()->guard('api')->user();

        // Cek role user
        if ($user->role == 'siswa') {

            return response()->json([
                'success' => true,
                'message' => 'Login berhasil',
                'user'    => $user
            ], 200);
        }

        return response()->json([
            'success' => true,
            'user'    => $user,    
            'token'   => $token   
        ], 200);
    }
}
