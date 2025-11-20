<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckAdminSession
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
        if($request->session()->get('role_id')=="1" || $request->session()->get('role_id')=="7"):
            if($request->session()->get('role_id')=="1"):
                return redirect()->route('dashboard');
            endif;
            if($request->session()->get('role_id')=="7"):
                return redirect()->route('accountant_dashboard');
            endif;
        endif;
        return $next($request);
    }
}
