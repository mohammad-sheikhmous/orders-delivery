<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\Profile;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    public function register(RegisterRequest $request)
    {
        try {
            DB::beginTransaction();

            $user = User::create([
                'mobile' => $request->mobile,
                'password' => $request->password,
            ]);
            $profile = Profile::create([
                'firstName' => $request->firstName,
                'lastName' => $request->lastName,
                'user_id' => $user->id,
            ]);

            DB::commit();
            return response()->json(['message' => 'the user created successfully but the number must be confirmed in order to become authenticated',
                'code' => $user->code], 201);
        } catch (Exception $exception) {
            DB::rollBack();
            return response()->json(['message' => 'something went wrong..!'],400);
        }
    }
}
