<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\password;
use function Laravel\Prompts\text;

/**
 * Creates an admin account, or gives an existing account admin access.
 *
 * php artisan cvforge:admin
 * php artisan cvforge:admin nama@domain.com
 */
class CreateAdmin extends Command
{
    protected $signature = 'cvforge:admin {email? : Email akun admin}';

    protected $description = 'Buat akun admin CVForge atau jadikan akun yang ada sebagai admin';

    public function handle(): int
    {
        $argument = $this->argument('email');
        $email = is_string($argument) && $argument !== '' ? $argument : text(
            label: 'Email',
            required: true,
            validate: fn (string $value) => Validator::make(['email' => $value], ['email' => ['email', 'max:255']])->errors()->first('email') ?: null,
        );
        $email = strtolower(trim($email));

        if (Validator::make(['email' => $email], ['email' => ['required', 'email', 'max:255']])->fails()) {
            $this->components->error('Email tidak valid.');

            return self::FAILURE;
        }

        $existing = User::query()->where('email', $email)->first();

        if ($existing !== null) {
            if ($existing->is_admin) {
                $this->components->info("{$email} sudah menjadi admin.");

                return self::SUCCESS;
            }

            if (! confirm(label: "Akun {$email} sudah ada. Jadikan admin?", default: true)) {
                $this->components->warn('Tidak ada perubahan.');

                return self::SUCCESS;
            }

            $existing->forceFill(['is_admin' => true])->save();
            $this->components->info("{$email} sekarang admin. Masuk lewat /login atau /admin.");

            return self::SUCCESS;
        }

        $name = text(label: 'Nama', required: true, validate: ['name' => 'max:255']);
        $secret = password(
            label: 'Kata sandi',
            required: true,
            validate: fn (string $value) => Validator::make(['password' => $value], ['password' => [Password::min(8)]])->errors()->first('password') ?: null,
            hint: 'Minimal 8 karakter. Tidak ditampilkan saat diketik.',
        );

        $user = User::query()->forceCreate([
            'name' => $name,
            'email' => $email,
            'password' => $secret,
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->components->info("Akun admin {$user->email} dibuat. Masuk lewat /login atau /admin.");

        return self::SUCCESS;
    }
}
