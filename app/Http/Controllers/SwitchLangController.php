<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SwitchLangController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke()
    {
        try {
            $status = user()->app_lang == 'en' ? 'ar' : 'en';

            user()->app_lang = $status;
            user()->save();

            app()->setLocale($status);

            return returnSuccessJson(__('messages.The App Language switched...'));

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
