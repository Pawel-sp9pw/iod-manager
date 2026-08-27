<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireTwoFactorSetup
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (! $user->two_factor_secret || ! $user->two_factor_confirmed_at)) {
            return redirect()->route('security.two-factor')
                ->with('warning', 'Aby korzystać z IOD Managera, włącz i potwierdź uwierzytelnianie dwuskładnikowe.');
        }

        return $next($request);
    }
}
