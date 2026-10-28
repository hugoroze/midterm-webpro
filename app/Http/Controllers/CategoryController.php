<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create(['name' => $request->name]);

        return redirect()->route('tasks.index')->with('success', 'Category added successfully.');
    }

    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('tasks.index')->with('success', 'Category deleted.');
    }
}