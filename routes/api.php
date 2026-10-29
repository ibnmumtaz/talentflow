<?php

use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // API routes are defined in routes/api_v1.php and loaded below.
    require __DIR__.'/api_v1.php';
});
