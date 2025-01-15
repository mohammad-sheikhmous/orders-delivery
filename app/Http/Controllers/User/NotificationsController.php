<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
    public function index()
    {
        try {
            $notifications = user()->notifications;

            if ($notifications->isEmpty())
                return returnErrorJson(__('messages.Not Found Notifications...!'), 400);
 
            return returnDataJson('notifications', $notifications, __('messages.All Notifications'));

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
