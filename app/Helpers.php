<?php

if (!function_exists('saveImages')) {
    function saveImages($folderName, $image)
    {
        $filename = $image->getClientOriginalName();
        \Illuminate\Support\Facades\Storage::disk('images')->putFileAs($folderName, $image, $filename);
        return $folderName . '/' . $filename;
    }
}

if (!function_exists('user')) {
    function user()
    {
        return auth()->user();
    }
}

if (!function_exists('returnExceptionJson')) {
    function returnExceptionJson()
    {
        return response()->json([
            'status' => false,
            'status code' => 400,
            'message' => __('messages.something went wrong...!')
        ], 400);
    }
}

if (!function_exists('returnErrorJson')) {
    function returnErrorJson(string $message, int $code, $key = 'message')
    {
        return response()->json([
            'status' => false,
            'status code' => $code,
            $key => $message,
        ], $code);
    }
}

if (!function_exists('returnSuccessJson')) {
    function returnSuccessJson(string $message, int $code = 200)
    {
        return response()->json([
            'status' => true,
            'status code' => $code,
            'message' => $message,
        ], $code);
    }
}

if (!function_exists('returnDataJson')) {
    function returnDataJson(string $key, $data, string $message = "", int $code = 200)
    {
        return response()->json([
            'status' => true,
            'status code' => $code,
            'message' => $message,
            $key => $data,
        ], $code);
    }
}
//\Illuminate\Support\Facades\Auth::logoutOtherDevices($request->password);
