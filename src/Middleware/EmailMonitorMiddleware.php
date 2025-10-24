<?php

namespace Laravel\EmailMonitor\Middleware;

use Closure;
use Illuminate\Http\Request;

class EmailMonitorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Add any middleware logic here if needed
        // For now, this is a placeholder middleware
        
        return $next($request);
    }
}

