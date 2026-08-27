<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyAccess
{
    public function handle(Request $request, Closure $next, string $mode = 'read'): Response
    {
        $company = $request->route('company');
        if (! $company instanceof Company) {
            abort(404);
        }

        $user = $request->user();
        if (! $user || ! $user->canAccessCompany($company->id)) {
            abort(403);
        }

        if ($mode === 'write' && ! $user->canWriteCompany($company->id)) {
            abort(403);
        }

        return $next($request);
    }
}
