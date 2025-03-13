<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\RoleUser;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->createAdmin();

//        $this->call([]);
    }

    protected function createAdmin(): void
    {
        $adminRole = Role::query()->updateOrCreate(
            ['name' => 'admin'],
            ['name' => 'admin']
        );

        $admin = User::query()->updateOrCreate(
            ['email' => env('ADMIN_EMAIL')],
            [
                'name' => 'Admin',
                'email' => env('ADMIN_EMAIL'),
                'password' => Hash::make(env('ADMIN_PASSWORD')),
            ]
        );

        RoleUser::query()->updateOrCreate(
            [
                'role_id' => $adminRole->id,
                'user_id' => $admin->id,
            ],
            [
                'role_id' => $adminRole->id,
                'user_id' => $admin->id,
            ]
        );
    }
}
