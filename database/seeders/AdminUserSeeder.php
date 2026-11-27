<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use RuntimeException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('services.craftyvok_admin.email');
        $password = config('services.craftyvok_admin.password');

        if (! $email || ! $password) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding the admin account.');
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => config('services.craftyvok_admin.name'),
                'password' => $password,
                'is_admin' => true,
            ],
        );
    }
}
