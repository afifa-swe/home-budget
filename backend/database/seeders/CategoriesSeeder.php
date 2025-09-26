<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $income = [
            'Заработная плата',
            'Иные доходы',
        ];

        $expense = [
            'Продукты питания',
            'Транспорт',
            'Мобильная связь',
            'Интернет',
            'Развлечения',
            'Другое',
        ];

        $user = \App\Models\User::first();
        if (! $user) {
            $user = \App\Models\User::factory()->create([
                'name' => 'Seed User',
                'email' => 'seed@example.com',
            ]);
        }

        foreach ($income as $name) {
            Category::firstOrCreate([
                'name' => $name,
                'user_id' => $user->id,
            ], ['type' => 'income']);
        }

        foreach ($expense as $name) {
            Category::firstOrCreate([
                'name' => $name,
                'user_id' => $user->id,
            ], ['type' => 'expense']);
        }
    }
}
