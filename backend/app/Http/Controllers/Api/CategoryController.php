<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $userId = $request->user()->id;
        $groups = Category::where('user_id', $userId)->get()->groupBy('type')->map(function ($items) {
            return $items->map(function ($i) { return ['id' => $i->id, 'name' => $i->name]; })->values();
        });

        $result = [
            'income' => $groups->get('income', collect())->toArray(),
            'expense' => $groups->get('expense', collect())->toArray(),
        ];

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $userId = $request->user()->id;
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                // unique per user
                Rule::unique('categories', 'name')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                }),
            ],
            'type' => ['required', Rule::in(['income', 'expense'])],
        ]);
        $data['user_id'] = $userId;
        $category = Category::create($data);
        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $userId = $request->user()->id;
        $category = Category::where('id', $id)->where('user_id', $userId)->firstOrFail();

        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('categories', 'name')->where(function ($query) use ($userId) {
                    return $query->where('user_id', $userId);
                })->ignore($category->id),
            ],
            'type' => [Rule::in(['income', 'expense'])],
        ]);

        $category->update($data);

        return response()->json($category);
    }

    public function destroy(Request $request, $id)
    {
        $userId = $request->user()->id;
        $category = Category::where('id', $id)->where('user_id', $userId)->firstOrFail();
        $category->delete();
        return response()->json(null, 204);
    }
}
