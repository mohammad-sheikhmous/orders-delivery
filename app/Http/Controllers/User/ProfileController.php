<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Http\Services\FcmService;
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
                'message' => __('messages.Profile information returned successfully...'),
                'profile' => $profile,
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    public function update(ProfileRequest $request)
    {
        try {
            $profile = user()->profile;

            $oldPhotoPath = $profile->photo;

            DB::beginTransaction();
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
dd(\user()->profile());
            if ($updated) {

                $profile = $profile->fresh();

                $message = __('messages.Profile information updated successfully...');

//                $fcmService = new FcmService();
//                $fcmService->FCM(
//                    user()->fcmTokens, [
//                    'title' => 'Profile updated',
//                    'message' => __('messages.Your profile has been modified.'),
//                ]);
            }
            DB::commit();

            $profile->mobile = user()->mobile;

            return response()->json([
                'status' => true,
                'status code' => 200,
                'message' => (isset($message)) ? $message : __('messages.No modifications have been made...'),
                'profile' => $profile,
            ]);

        } catch (\Exception $exception) {
            DB::rollBack();

            return returnExceptionJson();
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
                'message' => __('messages.The Account deleted with user logged out...'),
            ]);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
