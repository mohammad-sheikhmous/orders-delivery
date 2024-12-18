<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Rules\StrongPasswordRule;
use Illuminate\Http\Request;

class MobileController extends Controller
{
    public function verifyMobile(Request $request)
    {
        $request->validate([
            'mobile' => 'required',
            'code' => 'required'
        ]);

        try {
            $user = User::where('mobile', $request->mobile)->first();

            if (!$user || $user->code !== $request->code || now()->greaterThanOrEqualTo((string)$user->expired_at))
                return response()->json(['message' => 'invalid verification code'], 400);

            $user->resetCode();
            $token = $user->createToken('myToken', ['user'])->plainTextToken;

            return response()->json([
                'message' => 'Mobile number verified successfully',
                'token' => $token
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!',
            ], 400);
        }
    }

    public function sendResetCode(Request $request)
    {
        $request->validate(['mobile' => 'required|exists:users,mobile']);

        try {
            $user = User::where('mobile', $request->mobile)->first();
            $user->generateCode(); // Generate a 6-digit code

//        $client = new Client(getenv("TWILIO_SID"),getenv("TWILIO_TOKEN"));
//        $message = "Login OTP is ".$user->code;
//        $client->messages->create('+963'.$user->mobile,['from'=>'+44 20766028','body' => $message]);

            return response()->json([
                'message' => 'Reset code sent successfully.',
                'code' => $user->code,
            ]);
        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!',
            ], 400);
        }
    }

    public function resetPassword(Request $request)
    {
        $request->validate([
            'mobile' => ['required', 'exists:users,mobile'],
            'code' => 'required',
            'newPassword' => ['required', 'string', 'confirmed', 'min:8', new StrongPasswordRule()],
        ]);

        try {
            $user = User::where('mobile', $request->mobile)->first();

            if (!$user && $user->code !== $request->code && now()->greaterThanOrEqualTo($user->expired_at))
                return response()->json(['message' => 'invalid verification code'], 400);

            $user->password = $request->newPassword;
            $user->save();
            $user->resetCode();

            $token = $user->createToken('myToken', ['user'])->plainTextToken;
            return response()->json([
                'message' => 'Password changed successfully...',
                'token' => $token
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'message' => 'something went wrong...!',
            ], 400);
        }
    }
}
