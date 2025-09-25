<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use App\Http\Requests\StoreTransactionRequest;
use App\Http\Requests\UpdateTransactionRequest;
use Illuminate\Support\Facades\DB;

class TransactionController extends Controller
{
    public function index(Request $request)
    {
        $month = $request->query('month');

        $query = Transaction::query();

        if ($month) {
            [$year, $mon] = explode('-', $month);
            $query->whereYear('occurred_at', '=', $year)
                  ->whereMonth('occurred_at', '=', $mon);
        }

        $transactions = $query->orderBy('occurred_at', 'asc')->get();

        $running = 0;
        $result = $transactions->map(function ($t) use (&$running) {
            if ($t->type === 'income') {
                $running += $t->amount;
            } else {
                $running -= $t->amount;
            }

            return array_merge($t->toArray(), ['running_balance' => number_format($running, 2, '.', '')]);
        });

        return response()->json($result);
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $t = Transaction::create($data);
        return response()->json($t, 201);
    }

    public function update(UpdateTransactionRequest $request, $id)
    {
        $t = Transaction::findOrFail($id);
        $t->update($request->validated());
        return response()->json($t);
    }

    public function destroy($id)
    {
        $t = Transaction::findOrFail($id);
        $t->delete();
        return response()->json(null, 204);
    }
}
