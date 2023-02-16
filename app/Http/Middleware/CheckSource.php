<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckSource
{
    public function handle($request, Closure $next)
    {
        if ($request->headers->get('Referer') !== 'http://localhost:3000/') {
            return redirect('/api/invalid_url');
        }

        return $next($request);
    }
}
