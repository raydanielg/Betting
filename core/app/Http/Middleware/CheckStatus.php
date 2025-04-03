<?php

namespace App\Http\Middleware;

use Auth;
use Closure;

class CheckStatus {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        if (Auth::check()) {
            $user = auth()->user();
            if ($user->status && $user->ev && $user->sv && $user->tv) {
                return $next($request);
            } else {
                if ($request->is('api/*')) {
                    $notify[] = 'You need to verify your account first. Please logout and re-login';
                    return response()->json([
                        'remark'  => 'unverified',
                        'status'  => 'error',
                        'message' => ['error' => $notify],
                        'data'    => [
                            'user' => $user,
                        ],
                    ]);
                } else {
                    return to_route('user.authorization');
                }
            }
        }
        abort(403);
    }
}
