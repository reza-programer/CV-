<?php

namespace App\Http\Responses;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Laravel\Fortify\Fortify;
use Symfony\Component\HttpFoundation\Response;

/**
 * Admins land on the admin panel after signing in; everyone else on their CVs.
 */
class LoginResponse implements LoginResponseContract
{
    /**
     * @param  Request  $request
     */
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false]);
        }

        $user = $request->user();
        $redirect = redirect()->intended($user instanceof User && $user->is_admin ? '/admin' : Fortify::redirects('login'));

        // The admin panel is not an Inertia page, so an Inertia sign-in has to
        // leave with a full page visit instead of following the redirect.
        $path = (string) parse_url($redirect->getTargetUrl(), PHP_URL_PATH);

        if ($request->header('X-Inertia') && ($path === '/admin' || str_starts_with($path, '/admin/'))) {
            return Inertia::location($redirect->getTargetUrl());
        }

        return $redirect;
    }
}
