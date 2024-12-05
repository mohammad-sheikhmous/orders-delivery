<?php

namespace App\Observers;

use App\Models\User;

//require_once '/autoload.php';
use Illuminate\Http\Exceptions\HttpResponseException;
use Twilio\Rest\Client;

class UserObserver
{
    /**
     * Handle the User "created" event.
     */
    public function created(User $user): void
    {
        try {
            $user->generateCode();
//        $client = new Client(getenv("TWILIO_SID"),getenv("TWILIO_TOKEN"));
//        $message = "Login OTP is ".$user->code;
//        $client->messages->create('+963'.$user->mobile,['from'=>'+44 20766028','body' => $message]);
        } catch (\Exception $exception) {
            throw new HttpResponseException(response()->json([
                'message' => 'something went wrong...!'
            ],400));
        }
    }

    /**
     * Handle the User "updated" event.
     */
    public function updated(User $user): void
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     */
    public function deleted(User $user): void
    {
        //
    }

    /**
     * Handle the User "restored" event.
     */
    public function restored(User $user): void
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     */
    public function forceDeleted(User $user): void
    {
        //
    }

}
