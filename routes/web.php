<?php

use App\Http\Controllers\AuthorizationController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\RegisterEntryController;
use App\Http\Controllers\ReminderController;
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

        Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
        Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
        Route::get('/companies/{company}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
        Route::put('/companies/{company}', [CompanyController::class, 'update'])->name('companies.update');

        Route::get('/companies/{company}', function (Company $company) {
            return view('companies.show', [
                'company' => $company,
                'registersCount' => $company->registers()->count(),
                'activeAuthorizationsCount' => $company->authorizations()->whereNull('revoked_at')->count(),
                'upcomingReminders' => $company->reminders()->where('active', true)->orderByRaw('COALESCE(next_due_at, due_at) asc')->limit(8)->get(),
                'companyUsers' => $company->users()->orderBy('name')->get(),
            ]);
        })->middleware('company.access:read')->name('companies.show');

        Route::post('/companies/{company}/users', [CompanyUserController::class, 'store'])->name('companies.users.store');
        Route::delete('/companies/{company}/users/{user}', [CompanyUserController::class, 'destroy'])->name('companies.users.destroy');

        Route::get('/companies/{company}/registers', [RegisterController::class, 'index'])->middleware('company.access:read')->name('registers.index');
        Route::post('/companies/{company}/registers', [RegisterController::class, 'store'])->middleware('company.access:write')->name('registers.store');
        Route::get('/companies/{company}/registers/{register}', [RegisterController::class, 'show'])->middleware('company.access:read')->name('registers.show');
        Route::post('/companies/{company}/registers/{register}/entries', [RegisterEntryController::class, 'store'])->middleware('company.access:write')->name('register_entries.store');
        Route::delete('/companies/{company}/registers/{register}/entries/{entry}', [RegisterEntryController::class, 'destroy'])->middleware('company.access:write')->name('register_entries.destroy');

        Route::get('/companies/{company}/authorizations', [AuthorizationController::class, 'index'])->middleware('company.access:read')->name('authorizations.index');
        Route::post('/companies/{company}/authorizations', [AuthorizationController::class, 'store'])->middleware('company.access:write')->name('authorizations.store');
        Route::post('/companies/{company}/authorizations/{authorization}/revoke', [AuthorizationController::class, 'revoke'])->middleware('company.access:write')->name('authorizations.revoke');

        Route::get('/companies/{company}/reminders', [ReminderController::class, 'index'])->middleware('company.access:read')->name('reminders.index');
        Route::post('/companies/{company}/reminders', [ReminderController::class, 'store'])->middleware('company.access:write')->name('reminders.store');
        Route::post('/companies/{company}/reminders/{reminder}/complete', [ReminderController::class, 'complete'])->middleware('company.access:write')->name('reminders.complete');
    });
});
