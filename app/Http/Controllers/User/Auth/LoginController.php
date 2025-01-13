<?php

namespace App\Http\Controllers\User\Auth;

use App\Http\Controllers\Controller;
use App\Traits\ThrottlesLogins;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |The controller uses a trait to conveniently provide its functionality to your applications.
    |
    */
    use ThrottlesLogins;

    /**
     * Create a new controller instance.
     */

    public function __construct()
    {

    }

    /**
     * Handle a login request to the application.
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        try {
            if (method_exists($this, 'hasTooManyLoginAttempts') &&
                $this->hasTooManyLoginAttempts($request)) {
                $this->fireLockoutEvent($request);

                return $this->sendLockoutResponse($request);
            }

            if ($this->attemptLogin($request)) {
                return $this->sendLoginResponse($request);
            }

            // If the login attempt was unsuccessful we will increment the number of attempts
            // to login and redirect the user back to the login form. Of course, when this
            // user surpasses their maximum number of attempts they will get locked out.
            $this->incrementLoginAttempts($request);

            return $this->sendFailedLoginResponse($request);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }

    /**
     * Validate the user login request.
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => ['required', 'digits:10', 'starts_with:09'],
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     */
    protected function attemptLogin(Request $request)
    {
        return Auth::attempt(
            $this->credentials($request), $request->boolean('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     */
    protected function credentials(Request $request)
    {
        return $request->only($this->username(), 'password');
    }

    /**
     * Send the response after the user was authenticated.
     */
    protected function sendLoginResponse(Request $request)
    {
        $this->clearLoginAttempts($request);

        if (user()->verified !== 1) {
            user()->generateCode();

            return response()->json([
                'status' => false,
                'status code' => 401,
                'message' => 'The user has not confirmed his mobile yet. A verification code has been sent to confirm the mobile number.',
                'code' => user()->code,
            ], 401);
        }

        $token = user()->createToken('myToken', ['user'])->plainTextToken;

        return response()->json([
            'status' => true,
            'status code' => 200,
            'message' => 'logged in successfully',
            'user' => user(),
            'token' => $token
        ]);
    }

    /**
     * Get the failed login response instance.
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        return response()->json([
            'status' => false,
            'status code' => 401,
            $this->username() => [trans('auth.failed')],
        ], 401);
    }

    /**
     * Get the login username to be used by the controller.
     */
    public function username()
    {
        return 'mobile';
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        user()->tokens()->delete();

        return response()->json([
            'status' => true,
            'status code' => 200,
            'message' => 'logged out successfully...'
        ]);
    }
}

