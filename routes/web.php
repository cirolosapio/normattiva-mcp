<?php

declare(strict_types = 1);

use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('config/url', fn () => config('app.url'));
