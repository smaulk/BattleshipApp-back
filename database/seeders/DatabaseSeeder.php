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

        User::factory()->unverified()->create([
            'nickname' => 'user',
            'email' => 'user@mail.ru',
            'password' => 'user12345'
        ]);

        User::factory(5)->unverified()->create();
        User::factory(5)->create();
    }
}
