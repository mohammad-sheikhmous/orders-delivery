<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

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
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
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

            $profile = user()->profile()->update($request);

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'Profile information updated successfully...',
                'profile' => $profile,
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }

    public function destroy()
    {
        try {
            DB::beginTransaction();

            foreach (user()->orders as $order)
                $order->items()->delete();

            user()->orders()->delete();

            user()->cart->items()->delete();

            user()->cart()->delete();

            $photoPath = user()->profile->photo;
            if (isset($photoPath))
                Storage::disk('images')->delete($photoPath);

            user()->profile()->delete();

            user()->delete();

            DB::commit();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'The Account deleted with user logged out...',
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }
}
