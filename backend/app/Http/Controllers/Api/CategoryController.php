<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $groups = Category::all()->groupBy('type')->map(function ($items) {
            return $items->map(function ($i) { return ['id' => $i->id, 'name' => $i->name]; })->values();
        });

        // Ensure both keys exist
        $result = [
            'income' => $groups->get('income', collect())->toArray(),
            'expense' => $groups->get('expense', collect())->toArray(),
        ];

        return response()->json($result);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:100|unique:categories,name',
            'type' => ['required', Rule::in(['income', 'expense'])],
        ]);

        $category = Category::create($data);

        return response()->json($category, 201);
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);

        $data = $request->validate([
            'name' => ['required','string','max:100', Rule::unique('categories','name')->ignore($category->id)],
            'type' => [Rule::in(['income', 'expense'])],
        ]);

        $category->update($data);

        return response()->json($category);
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);
        $category->delete();

        return response()->json(null, 204);
    }
}
