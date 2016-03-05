<?php

namespace App\Http\Middleware;

use Closure;
use App\Transaction;

class VerifySession
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
        // If user already has a temporary transaction and it was removed via the ClearExpiredTransactions command
        // Remove the session token
        if ($request->session()->has('transaction_id')){
            if(!Transaction::find($request->session()->get('transaction_id'))){
                // Transaction does not exist. Delete Session key.
                $request->session()->forget('transaction_id');
            }
        }
        return $next($request);
    }
}
