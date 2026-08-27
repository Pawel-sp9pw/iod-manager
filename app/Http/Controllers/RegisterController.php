<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Register;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function index(Company $company): View
    {
        return view('registers.index', ['company' => $company, 'registers' => $company->registers()->withCount('entries')->orderBy('name')->get()]);
    }

    public function store(Request $request, Company $company): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:processing_activities,breaches,data_subject_requests,processors,dpia_risk,training,inspections,other'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);
        $register = $company->registers()->create($data + ['active' => true]);
        AuditLogger::record('register.created', $register, [], $register->toArray(), $company->id);
        return back()->with('status', 'Rejestr utworzony.');
    }

    public function show(Company $company, Register $register): View
    {
        abort_unless($register->company_id === $company->id, 404);
        return view('registers.show', ['company' => $company, 'register' => $register, 'entries' => $register->entries()->latest('event_date')->latest()->paginate(30)]);
    }
}
