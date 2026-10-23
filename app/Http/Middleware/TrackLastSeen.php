<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Stores when a signed-in user was last active, for the "online now" list
 * on the admin dashboard. Written at most once a minute per user and without
 * touching updated_at.
 */
class TrackLastSeen
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user instanceof User && ($user->last_seen_at === null || $user->last_seen_at->lt(now()->subMinute()))) {
            $now = now();
            $user->newQuery()->whereKey($user->getKey())->toBase()->update(['last_seen_at' => $now]);
            $user->setRawAttributes(['last_seen_at' => $now] + $user->getAttributes(), sync: true);
        }

        return $next($request);
    }
}
