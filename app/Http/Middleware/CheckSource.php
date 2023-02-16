<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckSource
{
    public function handle($request, Closure $next)
    {
        if ($request->headers->get('Referer') !== config('app.url')) {
            return redirect('/invalid_url');
        }

        return $next($request);
    }
}
