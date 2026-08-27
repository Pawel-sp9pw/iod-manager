<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;

class CreateAdmin extends Command
{
    protected $signature = 'iod:create-admin';
    protected $description = 'Create the first global IOD Manager administrator';

    public function handle(): int
    {
        $name = $this->ask('Name');
        $email = mb_strtolower((string) $this->ask('Email'));
        $password = $this->secret('Password (min. 14 chars, mixed case, number, symbol)');
        $confirmation = $this->secret('Repeat password');

        $validator = Validator::make([
            'name' => $name,
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $confirmation,
        ], [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(14)->mixedCase()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_super_admin' => true,
        ]);

        $this->info('Administrator created. 2FA will be required after first login.');
        return self::SUCCESS;
    }
}
