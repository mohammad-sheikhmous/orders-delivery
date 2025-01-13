<?php

namespace App\Http\Controllers;

use App\Models\FcmToken;
use http\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FcmTokensController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fcm_token' => 'required|string',
        ], [
            'fcm_tokens.required' => __('messages.The fcm token field is required.'),
            'fcm_tokens.string' => __('messages.The fcm token field must be string.')
        ]);

        if ($validator->fails()) {
            $messages = (string)collect($validator->customMessages)->first();
            return returnErrorJson($messages, 422, 'errors');
        }

        try {
            // Save or update the FCM token for the user
            FcmToken::updateOrCreate(
                ['user_id' => user()->id, 'fcm_token' => $request->fcm_token],
                ['fcm_token' => $request->fcm_token, 'device' => $request->device, 'updated_at' => now()]
            );

            return returnSuccessJson(__('messages.FCM token saved successfully'));

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
