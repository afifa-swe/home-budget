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

        foreach ($income as $name) {
            Category::firstOrCreate(['name' => $name], ['type' => 'income']);
        }

        foreach ($expense as $name) {
            Category::firstOrCreate(['name' => $name], ['type' => 'expense']);
        }
    }
}
