<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user) {
            return redirect()->guest(route('admin.login'));
        }

        abort_unless($user->is_admin, 403);

        $response = $next($request);

        // Stop browsers from showing cached admin pages after logout (back button).
        $response->headers->set('Cache-Control', 'no-store, private');

        return $response;
    }
}
