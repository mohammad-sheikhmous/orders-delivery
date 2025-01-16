<?php

use Illuminate\Support\Facades\Route;

require base_path('routes/admin-web.php');

Route::get('/', function () {
    return view('wawo');
});
