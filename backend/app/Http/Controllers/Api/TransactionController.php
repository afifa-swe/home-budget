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

        $query = Transaction::with('category');

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

            $arr = $t->toArray();
            $arr['running_balance'] = number_format($running, 2, '.', '');
            if (isset($t->category) && $t->category) {
                $arr['category'] = $t->category->name;
            }

            return $arr;
        });

        return response()->json($result);
    }

    public function store(StoreTransactionRequest $request)
    {
        $data = $request->validated();
        $t = Transaction::create($data);
        $t->load('category');
        $arr = $t->toArray();
        if ($t->category) $arr['category'] = $t->category->name;
        return response()->json($arr, 201);
    }

    public function update(UpdateTransactionRequest $request, $id)
    {
        $t = Transaction::findOrFail($id);
        $t->update($request->validated());
        $t->load('category');
        $arr = $t->toArray();
        if ($t->category) $arr['category'] = $t->category->name;
        return response()->json($arr);
    }

    public function destroy($id)
    {
        $t = Transaction::findOrFail($id);
        $t->delete();
        return response()->json(null, 204);
    }
}
