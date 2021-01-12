<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Route;

class RuleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $method = Route::current()->methods[0];
        $route = Route::currentRouteName();

        if ($method != 'POST' && $route != 'profile') {
            $role = auth()->user()->hasRule($route);
            if (!$role) {
                return redirect()->route('home');
            }
        }
        return $next($request);
    }
}