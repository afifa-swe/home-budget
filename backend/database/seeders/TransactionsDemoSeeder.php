<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class TransactionsDemoSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $examples = [
            ['type' => 'income', 'category' => 'Заработная плата', 'amount' => 200000.00, 'comment' => 'Monthly salary', 'daysAgo' => 3],
            ['type' => 'income', 'category' => 'Иные доходы', 'amount' => 150000.00, 'comment' => 'Freelance project', 'daysAgo' => 10],
            ['type' => 'expense', 'category' => 'Продукты питания', 'amount' => 45000.50, 'comment' => 'Groceries', 'daysAgo' => 5],
            ['type' => 'expense', 'category' => 'Транспорт', 'amount' => 5000.00, 'comment' => 'Monthly transit card', 'daysAgo' => 8],
            ['type' => 'expense', 'category' => 'Мобильная связь', 'amount' => 1200.99, 'comment' => 'Mobile bill', 'daysAgo' => 12],
            ['type' => 'expense', 'category' => 'Интернет', 'amount' => 800.00, 'comment' => 'Home internet', 'daysAgo' => 20],
            ['type' => 'expense', 'category' => 'Развлечения', 'amount' => 3000.00, 'comment' => 'Cinema and cafe', 'daysAgo' => 25],
            ['type' => 'expense', 'category' => 'Другое', 'amount' => 10000.00, 'comment' => 'Misc', 'daysAgo' => 29],
        ];

        foreach ($examples as $ex) {
            Transaction::create([
                'occurred_at' => $now->copy()->subDays($ex['daysAgo']),
                'type' => $ex['type'],
                'category' => $ex['category'],
                'amount' => $ex['amount'],
                'comment' => $ex['comment'],
            ]);
        }
    }
}
