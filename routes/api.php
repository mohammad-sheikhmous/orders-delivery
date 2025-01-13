<?php

use App\Http\Controllers\ShowImagesController;
use Illuminate\Support\Facades\Route;

require base_path('routes/admin-api.php');

require base_path('routes/user-api.php');

//const PAGINATION_COUNT = 5;

Route::get('/images/{name}', ShowImagesController::class)->where('name', '.*');



