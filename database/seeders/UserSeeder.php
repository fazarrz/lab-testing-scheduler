<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create users with different roles
        $users = [
            [
                'name' => 'Admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password123'), // Use a hashed password
                'role' => 'admin',  // Role admin
            ],
            [
                'name' => 'Ramdan',
                'email' => 'ramdan@example.com',
                'password' => Hash::make('password123'), // Use a hashed password
                'role' => 'engineer',  // Role engineer
            ],
            [
                'name' => 'Udin',
                'email' => 'udin@example.com',
                'password' => Hash::make('password123'), // Use a hashed password
                'role' => 'engineer',  // Role engineer
            ],
        ];

        foreach ($users as $userData) {
            // Create user
            $user = User::create($userData);
            
            // Optional: You can generate a JWT token for each user
            $token = JWTAuth::fromUser($user);
            echo "User created: {$user->name}, Token: {$token}\n"; // To verify token is generated
        }
    }
}
