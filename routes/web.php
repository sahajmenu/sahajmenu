<?php

declare(strict_types=1);

use App\Http\Middleware\ValidSubDomain;
use Illuminate\Support\Facades\Route;

Route::domain('{subdomain}.'.config('url.short'))
    ->middleware(ValidSubDomain::class)
    ->group(function (): void {
        Route::get('/', function () {
            return view('client-home');
        });
    });

Route::get('/', function () {
    return redirect()->route('filament.admin.auth.login');
});
