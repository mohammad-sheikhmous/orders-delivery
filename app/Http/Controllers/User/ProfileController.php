<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function show()
    {
        try {
            $profile = user()->selection();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'Profile information returned successfully...',
                'profile' => $profile,
            ]);

        } catch (\Exception $exception) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400));
        }
    }

    public function update(ProfileRequest $request)
    {
        try {
            //auth()->user()->update($request->only('mobile'));
            if ($request->photo) {
                $photoPath = saveImages('users', $request->photo);
                $request->replace('photo', $photoPath);
            }

            user()->profile()->update($request);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'Profile information updated successfully...',
            ]);

        } catch (\Exception $exception) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400));
        }
    }

    public function destroy()
    {
        try {
            user()->delete();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'The Account deleted with user logged out...',
            ]);

        } catch (\Exception $exception) {
            throw new HttpResponseException(response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400));
        }
    }
}
