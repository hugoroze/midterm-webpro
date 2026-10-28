<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    // Fonctionnalités 2, 5, 9 et 10 : Liste, Recherche, Filtre et Statistiques
    public function index(Request $request)
    {
        $categories = Category::all();

        $query = Task::with('category')->where('user_id', Auth::id());

        // Recherche par mot-clé
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filtrage par catégorie
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $tasks = $query->latest()->get();

        // Statistiques
        $totalTasks = Task::where('user_id', Auth::id())->count();
        $completedTasks = Task::where('user_id', Auth::id())->where('is_completed', true)->count();
        $pendingTasks = $totalTasks - $completedTasks;
        $totalCategories = Category::count();

        return view('tasks.index', compact(
            'tasks',
            'categories',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'totalCategories'
        ));
    }

    // Fonctionnalité 4 : Créer une tâche
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        Task::create([
            'user_id' => Auth::id(),
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
            'is_completed' => false,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tâche créée avec succès.');
    }

    // Afficher le formulaire de modification
    public function edit(Task $task)
    {
        $categories = Category::all();
        return view('tasks.edit', compact('task', 'categories'));
    }

    // Fonctionnalité 6 : Modifier une tâche
    public function update(Request $request, Task $task)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'description' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $task->update([
            'category_id' => $request->category_id,
            'title' => $request->title,
            'description' => $request->description,
            'due_date' => $request->due_date,
        ]);

        return redirect()->route('tasks.index')->with('success', 'Tâche mise à jour.');
    }

    // Fonctionnalité 8 : Basculer le statut (Terminée / En cours)
    public function toggle(Task $task)
    {
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return redirect()->route('tasks.index')->with('success', 'Statut de la tâche modifié.');
    }

    // Fonctionnalité 7 : Supprimer une tâche
    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Tâche supprimée.');
    }
}