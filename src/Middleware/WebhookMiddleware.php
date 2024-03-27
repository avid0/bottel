<?php
namespace Bottel\Middleware;

use Closure;

class WebhookMiddleware
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
        // Leave this method empty
        return $next($request);
    }
}
