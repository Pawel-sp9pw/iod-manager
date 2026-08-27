<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;
use Laravel\Fortify\Features;
use Laravel\Fortify\Fortify;

class FortifyServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Fortify::loginView(fn () => view('auth.login'));
        Fortify::twoFactorChallengeView(fn () => view('auth.two-factor-challenge'));

        Fortify::resetUserPasswordsUsing(function ($user, array $input): void {
            validator($input, [
                'password' => ['required', 'string', Password::min(14)->mixedCase()->numbers()->symbols(), 'confirmed'],
            ])->validate();

            $user->forceFill(['password' => $input['password']])->save();
        });

        RateLimiter::for('login', function (Request $request) {
            $key = mb_strtolower((string) $request->input(Fortify::username())).'|'.$request->ip();
            return Limit::perMinute(5)->by($key);
        });

        RateLimiter::for('two-factor', fn (Request $request) => Limit::perMinute(5)->by((string) $request->session()->get('login.id')));
    }
}
