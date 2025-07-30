<?php

namespace App\Http\Middleware;

use App\Affiliate;
use App\Affiliator;
use Closure;

class AffiliateTracking
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
        if ($request->has('ref')) {
            $affiliator = Affiliator::where('affiliator_code', $request->query('ref'))->first();
            if ($affiliator) {
                //dd($affiliator);

                if ($affiliator->status != 'active') {
                    return redirect()->route('home')->with('error', 'This affiliate link is inactive.');
                } else {
                    session(['ref_code' => $affiliator->affiliator_code]);
                    $affiliator->increment('clicks');
                }
            }
        }
        return $next($request);
    }
}
