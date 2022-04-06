<?php

namespace App\Http\Middleware;

use Closure;

class IfNotAdmin
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
        $user_type = auth()->user()->user_type;
        if($user_type !== 'ADMIN')
        {
            return redirect('/error404');
        }
        return $next($request);
    }
}
