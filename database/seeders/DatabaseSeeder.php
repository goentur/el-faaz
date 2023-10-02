<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Permission::create(['name' => 'peran pengguna']);
        Permission::create(['name' => 'pengguna']);
        Permission::create(['name' => 'dashboard']);

        $roleDeveloper = Role::create(['name' => 'developer']);

        $roleDeveloper->syncPermissions(['peran pengguna', 'pengguna']);

        $userDeveloper = User::factory()->create([
            'name' => 'Developer',
            'email' => 'dev@mail.com',
            'password' => bcrypt('a')
        ]);
        $userDeveloper->assignRole($roleDeveloper);
    }
}
