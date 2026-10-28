<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Fonctionnalité 1 : Créer une catégorie
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        Category::create(['name' => $request->name]);

        return redirect()->route('tasks.index')->with('success', 'Catégorie ajoutée avec succès.');
    }

    // Fonctionnalité 3 : Supprimer une catégorie
    public function destroy(Category $category)
    {
        $category->delete();
        return redirect()->route('tasks.index')->with('success', 'Catégorie supprimée.');
    }
}