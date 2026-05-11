<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('sentry', function () : void {
    throw new Exception('My first Sentry error!');
});
