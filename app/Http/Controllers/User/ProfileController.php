<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProfileRequest;
use App\Notifications\UsersNotification;
use App\Services\FirebaseService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    private $firebaseService;

    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function show()
    {
        try {
            $profile = user()->profile;
            $profile->mobile = user()->mobile;

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

            if ($updated) {

                $profile = $profile->fresh();

                $message = __('messages.Profile information updated successfully...');


                $token = user()->fcmTokens()->latest('updated_at')->pluck('fcm_token')->first();

                if ($token) {
                    $this->firebaseService->sendNotification($token, __('messages.Your Profile updated...'), __('messages.your profile has been updated'));

                    user()->notify(new UsersNotification(__('messages.Your Profile updated...'), __('messages.your profile has been updated')));
                }
            }
            DB::commit();

            return returnSuccessJson((isset($message)) ? $message : __('messages.No modifications have been made...'));

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

            return returnSuccessJson(__('messages.The Account deleted with user logged out...'));

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
