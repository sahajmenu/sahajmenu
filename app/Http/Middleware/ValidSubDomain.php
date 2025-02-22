<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Models\Client;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ValidSubDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $client = Client::subDomain($request->route('subdomain'))->first();
        if ($client) {
            return $next($request, $client);
        }
        abort(404);
    }
}
