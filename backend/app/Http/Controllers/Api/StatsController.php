<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StatsController extends Controller
{
    public function monthly(Request $request)
    {
        $year = $request->query('year', date('Y'));

        $rows = Transaction::selectRaw("EXTRACT(MONTH FROM occurred_at) AS month, SUM(CASE WHEN type='income' THEN amount ELSE 0 END) AS total_income, SUM(CASE WHEN type='expense' THEN amount ELSE 0 END) AS total_expense")
            ->whereYear('occurred_at', $year)
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->map(function ($r) {
                $r->balance = number_format($r->total_income - $r->total_expense, 2, '.', '');
                $r->month = (int)$r->month;
                $r->total_income = number_format($r->total_income, 2, '.', '');
                $r->total_expense = number_format($r->total_expense, 2, '.', '');
                return $r;
            });

        return response()->json($rows);
    }
}
