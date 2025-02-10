<?php

use App\Http\Middleware\ValidSubDomain;
use Illuminate\Support\Facades\Route;

Route::domain('{subdomain}.' . config('app.short_url'))
    ->middleware(ValidSubDomain::class)
    ->group(function () {
        // add route
    });

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});
