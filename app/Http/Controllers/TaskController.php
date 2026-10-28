<?php

namespace App\Http\Controllers;

use App\Models\Task;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $query = Task::with('category')->where('user_id', Auth::id());

        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        $tasks = $query->latest()->get();

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

        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function edit(Task $task)
    {
        $categories = Category::all();
        return view('tasks.edit', compact('task', 'categories'));
    }

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

        return redirect()->route('tasks.index')->with('success', 'Task updated successfully.');
    }

    public function toggle(Task $task)
    {
        $task->update([
            'is_completed' => !$task->is_completed
        ]);

        return redirect()->route('tasks.index')->with('success', 'Task status updated.');
    }

    public function destroy(Task $task)
    {
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }
}