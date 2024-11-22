<?php

use Illuminate\Support\Facades\Route;

// Welcome page
Route::get('/', function () {
    return view('welcome');
});

// Include additional route files
require __DIR__ . '/user.php';
require __DIR__ . '/admin.php';
