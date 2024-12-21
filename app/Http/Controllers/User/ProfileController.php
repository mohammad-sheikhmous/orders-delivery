<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Models\User;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function show()
    {
        try {
            $profile = user()->profile;
            $profile->mobile = user()->mobile;
//                User::where('id', user()->id)->selection()->get();

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
            $profile = user()->profile;

            $oldPhotoPath = $profile->photo;

            if (isset($request->image)) {
                if (isset($oldPhotoPath))
                    Storage::disk('images')->delete($oldPhotoPath);

                $photoPath = saveImages('users', $request->image);

                $request->merge(['photo' => $photoPath]);
            } else {
                if (isset($oldPhotoPath))
                    Storage::disk('images')->delete($oldPhotoPath);

                $request->merge(['photo' => null]);
            }

            $updated = user()->profile()->update($request->except('_method', 'image'));

            if ($updated) {

                $profile = $profile->fresh();

                $message = 'Profile information updated successfully...';
            }

            $profile->mobile = user()->mobile;

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => (isset($message)) ? $message : 'No modifications have been made...',
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
            $photoPath = user()->profile->photo;

            if (isset($photoPath))
                Storage::disk('images')->delete($photoPath);

            user()->delete();

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => 'The Account deleted with user logged out...',
            ]);

        } catch (\Exception $exception) {
            return response()->json([
                'status' => false,
                'status code' => 400,
                'message' => 'something went wrong...!'
            ], 400);
        }
    }
}
