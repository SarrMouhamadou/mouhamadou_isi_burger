<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        User::create([
            'name' => 'Mouhamadou Moustapha SARR',
            'username' => 'admin',
            'email' => 'msarrmoustapha@gmail.com',
            'password' => Hash::make('Hayati2406'),
            'role_id' => $adminRole->id,
            'email_verified_at' => now(),
        ]);
    }
}
