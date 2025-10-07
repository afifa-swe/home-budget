<?php

namespace Database\Factories;

use App\Models\Transaction;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TransactionFactory extends Factory
{
    protected $model = Transaction::class;

    public function definition()
    {
        return [
            'occurred_at' => $this->faker->dateTimeThisYear(),
            'type' => $this->faker->randomElement(['income', 'expense']),
            'category_id' => Category::factory(),
            'user_id' => User::factory(),
            'amount' => $this->faker->randomFloat(2, 1, 1000),
            'comment' => $this->faker->sentence(),
        ];
    }
}
