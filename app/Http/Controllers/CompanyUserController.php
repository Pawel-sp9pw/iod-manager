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
        $this->authorizeManagement($request, $company);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Password::min(14)->mixedCase()->numbers()->symbols()],
            'access_level' => ['required', 'in:admin,read_only'],
        ]);

        $email = mb_strtolower($data['email']);

        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $data['name'], 'password' => Hash::make($data['password'])]
        );

        $company->users()->syncWithoutDetaching([
            $user->id => [
                'role' => 'company_admin',
                'can_write' => $data['access_level'] === 'admin',
            ],
        ]);

        AuditLogger::record(
            'company.user_assigned',
            $company,
            [],
            [
                'assigned_user_id' => $user->id,
                'access_level' => $data['access_level'],
            ],
            $company->id
        );

        return back()->with('status', 'Konto zostało przypisane do firmy.');
    }

    public function update(Request $request, Company $company, User $user): RedirectResponse
    {
        $this->authorizeManagement($request, $company);

        abort_unless($company->users()->whereKey($user->id)->exists(), 404);
        abort_if(
            $user->id === $request->user()->id && ! $request->user()->is_super_admin,
            422,
            'Nie można zmienić własnego poziomu dostępu tym formularzem.'
        );

        $data = $request->validate([
            'access_level' => ['required', 'in:admin,read_only'],
        ]);

        $company->users()->updateExistingPivot($user->id, [
            'role' => 'company_admin',
            'can_write' => $data['access_level'] === 'admin',
        ]);

        AuditLogger::record(
            'company.user_access_updated',
            $company,
            ['assigned_user_id' => $user->id],
            [
                'assigned_user_id' => $user->id,
                'access_level' => $data['access_level'],
            ],
            $company->id
        );

        return back()->with('status', 'Poziom dostępu użytkownika został zmieniony.');
    }

    public function destroy(Request $request, Company $company, User $user): RedirectResponse
    {
        $this->authorizeManagement($request, $company);

        abort_if($user->id === $request->user()->id, 422, 'Nie można odpiąć własnego konta tym formularzem.');
        abort_unless($company->users()->whereKey($user->id)->exists(), 404);

        $company->users()->detach($user->id);

        AuditLogger::record(
            'company.user_removed',
            $company,
            ['assigned_user_id' => $user->id],
            [],
            $company->id
        );

        return back()->with('status', 'Dostęp użytkownika został usunięty.');
    }

    private function authorizeManagement(Request $request, Company $company): void
    {
        abort_unless($request->user()->canManageCompanyUsers($company->id), 403);
    }
}
