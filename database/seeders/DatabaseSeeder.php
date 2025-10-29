<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'user_id' => \Illuminate\Support\Str::uuid()->toString(),
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'admin@buydbyte.com',
            'password' => bcrypt('password'),
            'phone_number' => '1111222233',
            'role' => 'admin',
            'status' => 'active'
        ]);

        // Create normal user
        User::create([
            'user_id' => \Illuminate\Support\Str::uuid()->toString(),
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@buydbyte.com',
            'password' => bcrypt('password'),
            'phone_number' => '4444555566',
            'role' => 'customer',
            'status' => 'active'
        ]);

        // Run other seeders
        $this->call([
            CategoryAndProductSeeder::class,
        ]);
    }
}
