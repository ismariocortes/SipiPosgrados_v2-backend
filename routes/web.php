<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => 'SIPI Posgrados v2',
        'status' => 'running'
    ]);
});
