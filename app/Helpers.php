<?php

if (!function_exists('saveImages')){
    function saveImages($folderName,$image)
    {
        $filename = $image->getClientOriginalName();
        \Illuminate\Support\Facades\Storage::disk('images')->putFileAs($folderName,$image,$filename);
        return $folderName.'/'.$filename;
    }
}

if (!function_exists('user')){
    function user()
    {
        return auth()->user();
    }
}
