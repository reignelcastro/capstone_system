<?php

namespace App\Http\Middleware;

use Closure;

class IfAdmin
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
        $is_deactivated = auth()->user()->is_deactivated;
        if($is_deactivated == true)
        {
            return redirect('/error404');
        }

        return $next($request);
    }
}
