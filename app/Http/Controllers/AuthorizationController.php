<?php

namespace App\Http\Controllers;

use App\Models\Authorization;
use App\Models\Company;
use App\Services\AuditLogger;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;

class AuthorizationController extends Controller
{
    public function index(Company $company): View
    {
        return view('authorizations.index', ['company' => $company, 'authorizations' => $company->authorizations()->latest('issued_at')->paginate(40)]);
    }

    public function store(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'authorization_number' => ['nullable', 'string', 'max:100'],
            'person_name' => ['required', 'string', 'max:255'],
            'person_identifier' => ['nullable', 'string', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'scope' => ['required', 'string', 'max:10000'],
            'issued_at' => ['required', 'date'],
            'valid_until' => ['nullable', 'date', 'after_or_equal:issued_at'],
        ]);
        $authorization = $company->authorizations()->create($data + ['issued_by' => $request->user()->id]);
        AuditLogger::record('authorization.issued', $authorization, [], $authorization->toArray(), $company->id);
        return back()->with('status', 'Upoważnienie zostało wydane.');
    }

    public function pdf(Company $company, Authorization $authorization): Response
    {
        abort_unless($authorization->company_id === $company->id, 404);

        $pdf = Pdf::loadView('authorizations.pdf', [
            'company' => $company,
            'authorization' => $authorization,
        ])->setPaper('a4', 'portrait')
          ->setOption('defaultFont', 'DejaVu Sans');

        $number = $authorization->authorization_number ?: (string) $authorization->id;
        $filename = 'upowaznienie-' . Str::slug($number) . '-' . Str::slug($authorization->person_name) . '.pdf';

        return $pdf->download($filename);
    }

    public function revoke(Request $request, Company $company, Authorization $authorization): RedirectResponse
    {
        abort_unless($authorization->company_id === $company->id, 404);
        abort_if($authorization->revoked_at, 422, 'Upoważnienie jest już odwołane.');
        $data = $request->validate(['revocation_reason' => ['required', 'string', 'max:5000']]);
        $old = $authorization->toArray();
        $authorization->update(['revoked_at' => now(), 'revocation_reason' => $data['revocation_reason'], 'revoked_by' => $request->user()->id]);
        AuditLogger::record('authorization.revoked', $authorization, $old, $authorization->fresh()->toArray(), $company->id);
        return back()->with('status', 'Upoważnienie zostało odwołane, a historia zachowana.');
    }
}
