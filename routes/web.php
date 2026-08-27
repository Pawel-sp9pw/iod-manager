<?php

use App\Models\Company;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard');

Route::middleware('auth')->group(function () {
    Route::view('/security/two-factor', 'security.two-factor')->name('security.two-factor');

    Route::middleware('2fa.required')->group(function () {
        Route::get('/dashboard', function () {
            $user = request()->user();
            $companies = $user->is_super_admin
                ? Company::query()->where('active', true)->orderBy('name')->get()
                : $user->companies()->where('active', true)->orderBy('name')->get();

            return view('dashboard', compact('companies'));
        })->name('dashboard');

        Route::get('/companies/{company}', function (Company $company) {
            return view('companies.show', [
                'company' => $company,
                'registersCount' => $company->registers()->count(),
                'activeAuthorizationsCount' => $company->authorizations()->whereNull('revoked_at')->count(),
                'upcomingReminders' => $company->reminders()->where('active', true)->orderBy('next_due_at')->limit(8)->get(),
            ]);
        })->middleware('company.access:read')->name('companies.show');
    });
});
