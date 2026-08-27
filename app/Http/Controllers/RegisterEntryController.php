<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Register;
use App\Models\RegisterEntry;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class RegisterEntryController extends Controller
{
    public function store(Request $request, Company $company, Register $register): RedirectResponse
    {
        abort_unless($register->company_id === $company->id, 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'status' => ['required', 'string', 'max:50'],
            'event_date' => ['nullable', 'date'],
            'notes' => ['nullable', 'string', 'max:20000'],
            'data_json' => ['nullable', 'json'],
        ]);
        $entry = $register->entries()->create([
            'company_id' => $company->id,
            'title' => $data['title'],
            'status' => $data['status'],
            'event_date' => $data['event_date'] ?? null,
            'notes' => $data['notes'] ?? null,
            'data' => isset($data['data_json']) ? json_decode($data['data_json'], true) : null,
            'created_by' => $request->user()->id,
            'updated_by' => $request->user()->id,
        ]);
        AuditLogger::record('register_entry.created', $entry, [], $entry->toArray(), $company->id);
        return back()->with('status', 'Wpis dodany do rejestru.');
    }

    public function destroy(Request $request, Company $company, Register $register, RegisterEntry $entry): RedirectResponse
    {
        abort_unless($register->company_id === $company->id && $entry->register_id === $register->id && $entry->company_id === $company->id, 404);
        AuditLogger::record('register_entry.deleted', $entry, $entry->toArray(), [], $company->id);
        $entry->delete();
        return back()->with('status', 'Wpis przeniesiony do historii (soft delete).');
    }
}
