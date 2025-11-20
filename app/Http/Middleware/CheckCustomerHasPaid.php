<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;

class CheckCustomerHasPaid
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
        $user_id = $request->session()->get('user_id');
        
        if ($user_id) {
            $user = User::find($user_id);
            
            // If user has completed questionnaire but hasn't paid, redirect to payment
            if ($user && $user->is_locked == 1 && $user->is_payment_done == 0) {
                $request->session()->put('payment_user_id', $user_id);
                return redirect()->route('take_to_stripe_checkout');
            }
        }
        
        return $next($request);
    }
}
