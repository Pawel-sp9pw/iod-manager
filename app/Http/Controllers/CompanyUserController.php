<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\User;
use App\Services\AuditLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class CompanyUserController extends Controller
{
    public function store(Request $request, Company $company): RedirectResponse
    {
        abort_unless($request->user()->is_super_admin, 403);
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(14)->mixedCase()->numbers()->symbols()],
            'role' => ['required', 'in:iod,company_admin'],
            'can_write' => ['sometimes', 'boolean'],
        ]);

        $user = User::firstOrCreate(
            ['email' => mb_strtolower($data['email'])],
            ['name' => $data['name'], 'password' => Hash::make($data['password'])]
        );

        $company->users()->syncWithoutDetaching([
            $user->id => ['role' => $data['role'], 'can_write' => $data['role'] === 'iod' || $request->boolean('can_write')],
        ]);

        AuditLogger::record('company.user_assigned', $company, [], ['assigned_user_id' => $user->id, 'role' => $data['role']], $company->id);
        return back()->with('status', 'Konto zostało przypisane do firmy.');
    }

    public function destroy(Request $request, Company $company, User $user): RedirectResponse
    {
        abort_unless($request->user()->is_super_admin, 403);
        abort_if($user->id === $request->user()->id, 422, 'Nie można odpiąć własnego konta tym formularzem.');
        $company->users()->detach($user->id);
        AuditLogger::record('company.user_removed', $company, ['assigned_user_id' => $user->id], [], $company->id);
        return back()->with('status', 'Dostęp użytkownika został usunięty.');
    }
}
