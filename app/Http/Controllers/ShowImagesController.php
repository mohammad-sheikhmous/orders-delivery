<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class ShowImagesController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke($name)
    {
        try {
            $file = Storage::disk('images')->get($name);

            $type = Storage::disk('images')->mimeType($name);

            if (!$file)
                return returnErrorJson('Image not found.', 400);

            return response($file, 200)->header('Content-Type', $type);

        } catch (\Exception $exception) {
            return returnExceptionJson();
        }
    }
}
