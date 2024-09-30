<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->unverified()->create([
            'nickname' => 'user',
            'email'    => 'user@mail.ru',
            'password' => 'user12345'
        ]);

        $this->createRandomUsers(100);
    }

    /**
     * Создание случайных пользователей, с случайным подверждением почты
     */
    private function createRandomUsers(int $count): void
    {
        for ($i = 0; $i < $count; $i++) {
            if (rand(0, 1)) {
                User::factory()->unverified()->create();
            } else {
                User::factory()->create();
            }
        }
    }
}
