<?php

namespace App\Http\Middleware;

use App\Services\GuestSessionService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureGuestSession
{
    public function __construct(public GuestSessionService $guestSessions) {}

    /**
     * Ensure every unauthenticated customer browser session has a persistent server-side GuestSession UUID.
     *
     * @param  Closure(Request): Response  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if ($request->user() === null) {
            $this->guestSessions->ensure($request);
        }

        return $next($request);
    }
}
