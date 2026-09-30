<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class MakeSuperadmin extends Command
{
    protected $signature = 'library:make-superadmin {name?} {email?}';

    protected $description = 'Create the first superadmin account';

    public function handle(): int
    {
        $name = $this->argument('name') ?: $this->ask('Name');
        $email = $this->argument('email') ?: $this->ask('Email');

        if (! is_string($name) || trim($name) === '' || ! is_string($email) || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('A name and valid email address are required.');

            return self::FAILURE;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('A user already exists with that email address.');

            return self::FAILURE;
        }

        $password = $this->secret('Initial password (minimum 8 characters)');
        if (! is_string($password) || strlen($password) < 8) {
            $this->error('The password must contain at least 8 characters.');

            return self::FAILURE;
        }

        User::create([
            'name' => trim($name),
            'email' => strtolower($email),
            'password' => Hash::make($password),
            'role' => 'superadmin',
            'email_verified_at' => now(),
        ]);

        $this->info('Superadmin account created.');

        return self::SUCCESS;
    }
}
