<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            $user = User::create([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'mobile' => $request->mobile,
                'password' => $request->password,
            ]);

            return response()->json(['message' => 'the user created successfully but the number must be confirmed in order to become authenticated',
                'code'=>$user->code], 201);
        } catch (Exception $exception) {
            return response()->json(['message' => 'something went wrong..!']);
        }
    }
}
