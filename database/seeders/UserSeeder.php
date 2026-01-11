<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserSex;
use App\Enums\UserStatus;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
        'first_name' => 'Huy',
        'last_name' => 'Mai',
        'user_name' => 'huy',
        'email' => 'admin@email.com',
        'password' => Hash::make('123456789'),
        'birthday' => fake()->date('Y-m-d','-20 years'),
        'avatar'     => "https://i.pravatar.cc/150?u={huy}",
        'sex' => UserSex::MALE,
        'role' => UserRole::ADMIN,
        'status' => UserStatus::ACTIVE,
    ]);
    }
}
