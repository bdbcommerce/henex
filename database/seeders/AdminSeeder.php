<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        Role::firstOrCreate(['name' => 'editor',      'guard_name' => 'web']);

        $email    = env('ADMIN_EMAIL')    ?? throw new \RuntimeException('ADMIN_EMAIL is not set in .env');
        $password = env('ADMIN_PASSWORD') ?? throw new \RuntimeException('ADMIN_PASSWORD is not set in .env');
        $name     = env('ADMIN_NAME')     ?? throw new \RuntimeException('ADMIN_NAME is not set in .env');

        $admin = User::updateOrCreate(
            ['email' => $email],
            [
                'name'              => $name,
                'password'          => Hash::make($password),
                'email_verified_at' => now(),
            ]
        );

        $admin->syncRoles(['super_admin']);
    }
}
