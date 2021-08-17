<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class redirectIfHasntFilledCreditCard
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user = auth()->user();
        $user->hasPaymentMethod() ?: redirect()->to(route('settings.index'));
        return $next($request);
    }
}
