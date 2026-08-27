<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CompanyController extends Controller
{
    public function create(Request $request): View
    {
        abort_unless($request->user()->is_super_admin, 403);
        return view('companies.create');
    }

    public function store(Request $request): RedirectResponse
    {
        abort_unless($request->user()->is_super_admin, 403);
        $data = $this->validated($request);
        $company = Company::create($data);
        $company->users()->syncWithoutDetaching([$request->user()->id => ['role' => 'iod', 'can_write' => true]]);
        AuditLogger::record('company.created', $company, [], $company->toArray(), $company->id);
        return redirect()->route('companies.show', $company)->with('status', 'Firma została dodana.');
    }

    public function edit(Request $request, Company $company): View
    {
        abort_unless($request->user()->is_super_admin, 403);
        return view('companies.edit', compact('company'));
    }

    public function update(Request $request, Company $company): RedirectResponse
    {
        abort_unless($request->user()->is_super_admin, 403);
        $old = $company->toArray();
        $company->update($this->validated($request));
        AuditLogger::record('company.updated', $company, $old, $company->fresh()->toArray(), $company->id);
        return redirect()->route('companies.show', $company)->with('status', 'Dane firmy zapisane.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'short_name' => ['nullable', 'string', 'max:100'],
            'nip' => ['nullable', 'string', 'max:20'],
            'regon' => ['nullable', 'string', 'max:20'],
            'krs' => ['nullable', 'string', 'max:20'],
            'email' => ['nullable', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'postal_code' => ['nullable', 'string', 'max:20'],
            'city' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:10000'],
            'active' => ['sometimes', 'boolean'],
        ]) + ['active' => $request->boolean('active', true)];
    }
}
